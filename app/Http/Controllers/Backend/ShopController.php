<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Models\Shop;
use App\Models\TSO;
use App\Models\ShopVisit;
use App\Models\Distributor;
use App\Models\Route;
use App\Models\SubRoutes;
use App\Models\RouteDay;
use App\Models\City;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreShopRequest;
use App\Http\Requests\UpdateShopRequest;
use App\Helpers\MasterFormsHelper;
use Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Jobs\SendSmsJob;
use Yajra\DataTables\DataTables;
use App\Exports\ShopsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\RequestNotification;
use App\Models\RouteTso;

class ShopController extends Controller
{


    public function __construct()
    {
        $this->page = 'pages.Shop.Shops.';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index_Add_multi(Request $request, Shop $shop)
    {
        if ($request->ajax()) {
            $count = Shop::status()->count();

            $shop = $shop::status()
                ->with(['Distributor', 'tsos', 'Route', 'subroutes']) // Updated to match relationship names
                ->when($request->distributor_id != null, function ($query) use ($request) {
                    $query->where('distributor_id', $request->distributor_id);
                })
                ->when($request->distributor_id == null, function ($query) use ($request) {
                    $distributor = MasterFormsHelper::get_users_distributors(Auth::user()->id);
                    $query->whereIn('distributor_id', $distributor);
                })
                ->when($request->tso_id, function ($q) use ($request) {
                    $q->whereHas('tsos', function ($t) use ($request) {
                        $t->where('tso.id', $request->tso_id);
                    });
                })
                ->when($request->route_id != null, function ($query) use ($request) {
                    $query->where('route_id', $request->route_id);
                })
                ->when($request->city != null, function ($query) use ($request) {
                    $query->whereHas('Distributor', function ($q) use ($request) {
                        $q->whereIn('city_id', $request->city);
                    });
                })
                ->when($request->date != null, function ($query) use ($request) {
                    $query->whereDate('created_at', $request->date);
                })
                ->select(
                    'shop_code',
                    DB::raw('DATE(created_at) as created_at'),
                    'company_name',
                    'shop_zone_id',
                    'distributor_id',
                    'tso_id',
                    'route_id',
                    'id',
                    'sub_route_id',
                    'city',
                    'image',
                    'active',
                    'status_username',
                    'status_user_id',
                    'remarks'
                );

                $statusMapping = [
                    'activate' => 1,
                    'activate request' => 2,
                    'deactivate request' => 3,
                    'deactivate' => 0,
                    'new shop create' => 4,
                ];

                return DataTables::of($shop)
                    ->addIndexColumn()
                    ->editColumn('city', function ($row) {
                        return optional($row->Distributor)->city ?? '--';
                    })
                    ->editColumn('distributor', function ($row) {
                        return optional($row->Distributor)->distributor_name ?? '--';
                    })
                    ->filterColumn('distributor', function ($query, $keyword) {
                        $query->whereHas('Distributor', function ($q) use ($keyword) {
                            $q->where('distributor_name', 'like', "%$keyword%");
                        });
                    })
                    ->editColumn('tso', function ($row) {
                        return $row->tsos->pluck('name')
                                        ->implode(', ')
                            ?: '--';
                    })
                    ->filterColumn('tso', function ($query, $keyword) {
                        $query->whereHas('tsos', function ($q) use ($keyword) {
                            $q->where('name', 'like', "%$keyword%");
                        });
                    })
                    // For main routes
                    ->editColumn('route', function ($row) {
                        return optional($row->Route)->route_name ?? '--';
                    })
                    ->filterColumn('route', function ($query, $keyword) {
                        $query->whereHas('Route', function ($q) use ($keyword) {
                            $q->where('route_name', 'like', "%$keyword%");
                        });
                    })

                    // For subroutes
                    ->editColumn('sub_route', function ($row) {
                        return optional($row->subroutes)->route_name ?? '--';
                    })
                    ->filterColumn('sub_route', function ($query, $keyword) {
                        $query->whereHas('subroutes', function ($q) use ($keyword) {
                            $q->where('name', 'like', "%$keyword%");
                        });
                    })
                    ->editColumn('created_at', function ($row) {
                        return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y');
                    })
                    ->editColumn('status', function ($row) use ($statusMapping) {
                        switch ($row->active) {
                            case 1:
                                return 'Activate';
                            case 2:
                                return 'Activate Request' . ((Auth::user()->id != $row->status_user_id) ? ' (' . $row->status_username . ')' : '');
                            case 3:
                                return 'Deactivate Request' . ((Auth::user()->id != $row->status_user_id) ? ' (' . $row->status_username . ')' : '');
                            case 0:
                                return 'Deactivate';
                            case 4:
                                return 'New Shop Create';
                            default:
                                return '--';
                        }
                    })
                    ->filterColumn('status', function ($query, $keyword) use ($statusMapping) {
                        $keyword = strtolower($keyword);
                        $matchedStatus = [];

                        foreach ($statusMapping as $key => $value) {
                            if (strpos($key, $keyword) !== false) {
                                $matchedStatus[] = $value;
                            }
                        }

                        if (!empty($matchedStatus)) {
                            $query->whereIn('active', $matchedStatus);
                        }
                    })
                    ->orderColumn('status', function ($query, $order) use ($statusMapping) {
                        $query->orderByRaw('FIELD(active, ' . implode(',', $statusMapping) . ') ' . $order);
                    })
                    ->addColumn('action', function ($row) {
                        $dropdownHtml = '<div class="dropdown">
                            <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                            <div class="dropdown-menu dropdown-menu_sale_order_list" aria-labelledby="dropdownMenuButton">
                                <a href="' . route('shop.show', $row->id) . '" class="dropdown-item_sale_order_list dropdown-item">View</a>';

                        if (Auth::user()->can('Shop_Edit')) {
                            $dropdownHtml .= '<a href="' . route('shop_edit_multi', $row->id) . '" class="dropdown-item_sale_order_list dropdown-item">Edit</a>';
                        }

                        if (Auth::user()->can('Shop_Delete')) {
                            $dropdownHtml .= '<a href="javascript:void(0);" data-url="' . route('shop.destroy', $row->id) . '" id="delete-user" class="dropdown-item_sale_order_list dropdown-item">Delete</a>';
                        }

                        if ($row->active == 0 && Auth::user()->can('Shop_Activate')) {
                            $dropdownHtml .= '<a href="javascript:void(0);" data-text="You want to activate this shop" data-url="' . route('shop.active', $row->id) . '" class="dropdown-item_sale_order_list dropdown-item" id="active-record">Activate</a>';
                        } elseif ($row->active == 1 && Auth::user()->can('Shop_Deactivate')) {
                            $dropdownHtml .= '<a href="javascript:void(0);" data-text="You want to deactivate this shop" data-url="' . route('shop.inactive', $row->id) . '" class="dropdown-item_sale_order_list dropdown-item" id="inactive-record">Deactivate</a>';
                        }

                        $dropdownHtml .= '</div></div>';
                        return $dropdownHtml;
                    })
                    ->make(true);
        }

        return view($this->page . 'ShopListMulti');
    }

    public function index(Request $request, Shop $shop)
    {


      //  $count =  Shop::status()->count();

       if ($request->ajax()):
    //    if ($request->submit || $request->page):
        // dd($request->date);
        $count =  Shop::status()->count();

        $shop =  $shop::status()
        ->with(['Distributor', 'tso', 'route', 'subroutes'])
        ->when($request->distributor_id!=null, function ($query) use ($request){
            $query->where('distributor_id',$request->distributor_id);
        })
        ->when($request->distributor_id==null, function ($query) use ($request){

            $distributor = MasterFormsHelper::get_users_distributors(Auth::user()->id);
            $query->whereIn('distributor_id',$distributor);
        })
        ->when($request->tso_id!=null , function ($query) use ($request) {
         $query->where('tso_id',$request->tso_id);
        })
        ->when($request->route_id!=null , function ($query) use ($request){
            $query->where('route_id',$request->route_id);
        })
        ->when($request->city!=null , function ($query) use ($request){
            $query->whereHas('Distributor', function ($q) use ($request) {
                $q->whereIn('city_id', $request->city); // Adjust 'city_id' as per your column name in the Distributor model
            });
            // $query->whereIn('city',$request->city);
            // $query->whereIn('city',$request->city);
        })
        ->when($request->date!=null , function ($query) use ($request) {
            $query->whereDate('created_at',$request->date);
        }) // Eager load related models
        ->select('shop_code','company_name','shop_zone_id','distributor_id','tso_id','route_id','id','sub_route_id','city','image', 'active','status_username','status_user_id','remarks')
        // ->paginate(100);
        // ->orderBy('distributor_id', 'ASC')
        // ->orderBy('company_name', 'ASC');
        ;
        // dd($shop->first());


        $statusMapping = [
            'activate' => 1,
            'activate request' => 2,
            'deactivate request' => 3,
            'deactivate' => 0,
            'new shop create' => 4,
        ];

        return DataTables::of($shop)
                ->addIndexColumn()
                ->editColumn('city', function($row) {
                    return $row->Distributor->city;
                })
                ->editColumn('distributor', function($row) {
                    return $row->Distributor ? $row->Distributor->distributor_name : '';
                })
                ->filterColumn('distributor', function($query, $keyword) {
                    $query->whereHas('Distributor', function($q) use ($keyword) {
                        $q->where('distributor_name', 'like', "%$keyword%");
                    });
                })
                ->editColumn('tso', function($row) {
                    return $row->tso->name;
                })
                ->filterColumn('tso', function($query, $keyword) {
                    $query->whereHas('tso', function($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    });
                })
                ->editColumn('route', function($row) {
                    return $row->route->route_name ?? '--';
                })
                ->filterColumn('route', function($query, $keyword) {
                    $query->whereHas('route', function($q) use ($keyword) {
                        $q->where('route_name', 'like', "%$keyword%");
                    });
                })
                ->editColumn('sub_route', function($row) {
                    return $row->subroutes->route_name ?? '--';
                })
                ->filterColumn('sub_route', function($query, $keyword) {
                    $query->whereHas('subroutes', function($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    });
                })
                ->editColumn('status', function($row) {
                    // return $row->active;
                    switch ($row->active) {
                        case 1: return 'Activate' ;
                        case 2: return 'Activate Request' .  ((Auth::user()->id  != $row->status_user_id) ? '('. $row->status_username .')' : '');
                        case 3: return 'Deactivate Request' . ((Auth::user()->id  != $row->status_user_id) ? '('. $row->status_username .')' : '');
                        case 0: return 'Deactivate';
                        case 4: return 'New Shop Create';
                        default: return '--';
                    }
                })
                ->filterColumn('status', function($query, $keyword) use ($statusMapping) {
                    $keyword = strtolower($keyword);

                    $matchedStatus = [];
                    foreach ($statusMapping as $key => $value) {
                        if (strpos($key, $keyword) !== false) {
                            $matchedStatus[] = $value;
                            // break;
                        }
                    }

                    if (!empty($matchedStatus)) {
                        $query->whereIn('active', $matchedStatus);
                    }
                })
                ->orderColumn('status', function ($query, $order) use ($statusMapping) {
                    // Custom order logic: replace `CASE` with the mapping order
                    $query->orderByRaw('FIELD(active, ' . implode(',', $statusMapping) . ') ' . $order);
                })
                ->addColumn('action', function($row) {
                    // return '<a href="/users/' . $row->id . '/edit" class="btn btn-sm btn-primary">Edit</a>';
                    $dropdownHtml = '<div class="dropdown">
                        <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                        <div class="dropdown-menu dropdown-menu_sale_order_list" aria-labelledby="dropdownMenuButton">
                            <a href="' . route('shop.show', $row->id) . '" class="dropdown-item_sale_order_list dropdown-item">View</a>';

                        // Check permissions and add Edit link if the user has permission
                        if (Auth::user()->can('Shop_Edit')) {
                            $dropdownHtml .= '<a href="' . route('shop.edit', $row) . '" class="dropdown-item_sale_order_list dropdown-item">Edit</a>';
                        }

                        // Check permissions and add Delete link if the user has permission
                        if (Auth::user()->can('Shop_Delete')) {
                            $dropdownHtml .= '<a href="javascript:void(0);" data-url="' . route('shop.destroy', $row->id) . '" id="delete-user" class="dropdown-item_sale_order_list dropdown-item">Delete</a>';
                        }

                        // Add Activate/Deactivate options based on the shop's active status
                        if ($row->active == 0 && Auth::user()->can('Shop_Activate')) {
                            $dropdownHtml .= '<a href="javascript:void(0);" data-text="You want to activate this shop" data-url="' . route('shop.active', $row->id) . '" class="dropdown-item_sale_order_list dropdown-item" id="active-record">Activate</a>';
                        } elseif ($row->active == 1 && Auth::user()->can('Shop_Deactivate')) {
                            $dropdownHtml .= '<a href="javascript:void(0);" data-text="You want to deactivate this shop" data-url="' . route('shop.inactive', $row->id) . '" class="dropdown-item_sale_order_list dropdown-item" id="inactive-record">Deactivate</a>';
                        }

                        $dropdownHtml .= '</div>
                    </div>';
                    return $dropdownHtml;

                })

                ->make(true);

            return  view($this->page . 'ShopListAjax', compact('shop'));
        endif;
        return  view($this->page . 'ShopList');
    }



    public function exportShops(Request $request)
    {
        $filters = $request->only(['distributor_id', 'tso_id', 'city', 'route_id', 'date']);
        // dd($filters);
        return Excel::download(new ShopsExport($filters), 'shops.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Shop $shop)
    {
        $uniqe_no = $shop->UniqueNo();
        return view($this->page . 'AddShop', compact('uniqe_no'));
    }

 public function create_create_multi(Shop $shop)
    {
        $uniqe_no = $shop->UniqueNo();
        return view($this->page . 'AddShopMulti', compact('uniqe_no'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreShopRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->only('shop_code' , 'custome_code' , 'company_name' , 'title' , 'contact_person' , 'email' ,
            'alt_email' , 'phone' , 'mobile_no' , 'alt_phone' , 'address' , 'address_2' , 'city', 'state' , 'zip_code' , 'note' ,
            'filer' , 'cnic' , 'allow_credit_days' , 'allow_credit_amount' , 'delvery_days' , 'invoice_discount' , 'shop_type_id' ,
            'shop_zone_id' , 'balance_amount' , 'debit_credit' , 'map' , 'latitude' , 'longitude' , 'location_radius',
            'distributor_id' , 'tso_id' , 'route_id' , 'sub_route_id');
            // $data = $request->all();
            $data['mobile_no'] = MasterFormsHelper::correctPhoneNumber($data['mobile_no']);
            if ($request->shop_location != 1) {
                $data['latitude'] = null;
                $data['longitude'] = null;
                $data['location_radius'] = null;
            }

            if ($request->file('image')) {
                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();

                // delete previous image
                // if (Storage::disk('public')->exists($product->image)) {
                //     Storage::disk('public')->delete($product->image);
                // }

                $file->storeAs('shop_image', $fileName, 'public'); // 'uploads' is the directory to store files.
            }
            $data['image'] = $fileName;

            $shop =  Shop::Create($data);

            // SendSmsJob::dispatch( $data['mobile_no'] , "Dear $request->contact_person,\n Welcome to Smile Food Pakistan");
            // MasterFormsHelper::sendSmsNotification( $data['mobile_no'] , 'Welcome');
            DB::commit();
            return response()->json(['success' => 'Shop Created Successfully']);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            dd($th->getMessage());
            return response()->json(['catchError' => $th->getMessage()]);
        }
    }

 public function store_multi(StoreShopRequest $request)
{
    DB::beginTransaction();
    
    try {
        // Get all input except the relationship data
        $data = $request->except('tso_id');
        
        // Format phone number
        $data['mobile_no'] = MasterFormsHelper::correctPhoneNumber($data['mobile_no']);
        
        // Handle location data
        if ($request->shop_location != 1) {
            $data['latitude'] = null;
            $data['longitude'] = null;
            $data['location_radius'] = null;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '-' . $file->getClientOriginalName();
            $file->storeAs('shop_image', $fileName, 'public');
            $data['image'] = $fileName;
        }

        // Create the shop
        $shop = Shop::create($data);
        
        // Handle TSO relationships
        if ($request->filled('tso_id')) {
            // Ensure we're working with an array of integers
            $tsoIds = is_array($request->tso_id) 
                    ? array_map('intval', $request->tso_id)
                    : [(int)$request->tso_id];
            
            // Use sync to manage relationships (removes old, adds new)
            $shop->tsos()->sync($tsoIds);
        }

        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Shop created successfully',
            'data' => $shop
        ]);
        
    } catch (\Throwable $th) {
        DB::rollback();
        
        // Log the error for debugging
        \Log::error('Shop creation failed: ' . $th->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Error creating shop',
            'error' => env('APP_DEBUG') ? $th->getMessage() : 'An error occurred'
        ], 500);
    }
}
 public function shop_edit_multi(Shop $shop)
{
    
    return view($this->page . 'EditShop_Multi', compact('shop'));
}


public function shop_update_multi(Request $request, Shop $shop)
{
   
    DB::beginTransaction();
    try {
        $data = $request->except('tso_id', 'shop_location');
        
        // Shop location check
        if ($request->shop_location != 1) {
            $data['latitude'] = null;
            $data['longitude'] = null;
            $data['location_radius'] = null;
        }

        // Phone number correction
        $data['mobile_no'] = MasterFormsHelper::correctPhoneNumber($data['mobile_no']);

        // Image handling
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '-' . $file->getClientOriginalName();

            // Delete previous image
            if ($shop->image && Storage::disk('public')->exists('shop_image/' . $shop->image)) {
                Storage::disk('public')->delete('shop_image/' . $shop->image);
            }

            $file->storeAs('shop_image', $fileName, 'public');
            $data['image'] = $fileName;
        }

        // Update the shop
        $shop->update($data);

        // Update TSO relationships
        if ($request->filled('tso_id')) {
            $tsoIds = is_array($request->tso_id) 
                    ? array_map('intval', $request->tso_id)
                    : [(int)$request->tso_id];
            
            $shop->tsos()->sync($tsoIds);
        }

        DB::commit();
        return response()->json(['success' => 'Updated successfully.']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // dd($id);
        $show_data = Shop::find($id);
        return view('pages.Shop.Shops.view' , compact('show_data'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Shop $shop)
    {
        return  view($this->page . 'EditShop', compact('shop'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateShopRequest $request, Shop $shop)
    {
        // dd($shop , $request->shop_location , $request->all());
        $data = $request->only('shop_code' , 'custome_code' , 'company_name' , 'title' , 'contact_person' , 'email' ,
        'alt_email' , 'phone' , 'mobile_no' , 'alt_phone' , 'address' , 'address_2' , 'city', 'state' , 'zip_code' , 'note' ,
        'filer' , 'cnic' , 'allow_credit_days' , 'allow_credit_amount' , 'delvery_days' , 'invoice_discount' , 'shop_type_id' ,
        'shop_zone_id' , 'balance_amount' , 'debit_credit' , 'map' , 'latitude' , 'longitude' , 'location_radius',
        'distributor_id' , 'tso_id' , 'route_id' , 'sub_route_id');
        if ($request->shop_location != 1) {
            $data['latitude'] = null;
            $data['longitude'] = null;
            $data['location_radius'] = null;
        }


        if ($request->file('image')) {
            $file = $request->file('image');
            $fileName = time() . '-' . $file->getClientOriginalName();

            // delete previous image
            if (Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $file->storeAs('shop_image', $fileName, 'public'); // 'uploads' is the directory to store files.
            $data['image'] = $fileName;
        }

        $shop = $shop->update($data);
        return response()->json(['success' => 'Updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Shop::where('id', $id)->update(['status' => 0]);
        return response()->json(['success' => 'Deleted Successfully!']);
    }


    public function shop_active($id)
    {
        if (Auth::user()->hasAnyRole(['CEO','Super Admin'])) {
            Shop::where('id', $id)->update(['active' => 1 , 'status_username' => Auth::user()->name , 'status_user_id' => Auth::user()->id]);
            $heading = 'Activated!';
            $message = 'Activate Successfully!';
        }
        else{
            Shop::where('id', $id)->update(['active' => 2 , 'status_username' => Auth::user()->name , 'status_user_id' => Auth::user()->id]);
            $heading = 'Request Submitted!';
            $message = 'Activate Request Submitted!';
        }

        return response()->json(['success' => $message,'heading'=>$heading]);
    }

    public function shop_inactive($id)
    {
        if (Auth::user()->hasAnyRole(['CEO','Super Admin'])) {
            Shop::where('id', $id)->update(['active' => 0 , 'status_username' => Auth::user()->name , 'status_user_id' => Auth::user()->id]);
            $message = 'Deactivate Successfully!';
            $heading = 'Deactivated!';
        }
        else {
            Shop::where('id', $id)->update(['active' => 3 , 'status_username' => Auth::user()->name , 'status_user_id' => Auth::user()->id]);
            $message = 'Deactivate Request Submitted!';
            $heading = 'Request Submitted!';
        }
        return response()->json(['success' => $message,'heading'=>$heading]);
    }



    public function shop_status_request(Request $request , Shop $shop)
    {
        if ($request->ajax())
        {
            $status_request = $request->status_request;

            $count =  Shop::status()->count();

            $shop =  $shop::status()
            ->where('active' , $status_request)
            ->when($request->distributor_id!=null, function ($query) use ($request){
                $query->where('distributor_id',$request->distributor_id);
            })
            ->when($request->distributor_id==null, function ($query) use ($request){

                $distributor = MasterFormsHelper::get_users_distributors(Auth::user()->id);
                $query->whereIn('distributor_id',$distributor);
            })
            ->when($request->tso_id!=null , function ($query) use ($request) {
            $query->where('tso_id',$request->tso_id);
            })
            ->when($request->route_id!=null , function ($query) use ($request){
                $query->where('route_id',$request->route_id);
            })
            ->when($request->city!=null , function ($query) use ($request){
                $query->whereIn('city',$request->city);
            })

            ->select('shop_code','company_name','shop_zone_id','distributor_id','tso_id','route_id','id','sub_route_id','city','image', 'active','status_username','status_user_id','remarks')
            // ->paginate(100);
            ->orderBy('distributor_id', 'ASC')
            ->orderBy('company_name', 'ASC')
            ->get();

            return  view($this->page . 'ApprovalStatausShopAjax' , compact('shop' , 'status_request'));

        }
        return  view($this->page . 'ApprovalStatausShop');
    }


    public function shop_status_request_post(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $ids = $request->checked_records;
            $status = $request->status;
            // dd($ids , $status , $request->all());
            if ($status == 3) {
                if ($request->approve_reject == 1) {
                    $message = 'Deactivate Successfully!';
                    $m = 'Deactivate Request Approve';
                    $active = 0;
                }
                else
                {
                    $message = 'Deactivate Request Rejected!';
                    $m = 'Deactivate Request Reject';
                    $active = 1;
                }
                // dd($message);
                foreach (Shop::whereIn('id', $ids)->get() as $key => $row) {
                    $user = User::find(Auth::id());
                    $user2 = User::find($row->status_user_id);
                    // dd($row->status_user_id);
                    $msg = 'Shop (' .$row->company_name. ') '. $m .' by '.$user->name;
                    $user2->notify(new RequestNotification($user2 , $user , $msg , route('shop.index')));
                }


                Shop::whereIn('id', $ids)->update(['active' => $active , 'status_username' => Auth::user()->name , 'status_user_id' => Auth::user()->id , 'remarks' => $request->remarks]);

            }
            else
            {
                if ($request->approve_reject == 1) {
                    $message = 'Activate Successfully!';
                    $m = 'Activate Request Approve';
                    $active = 1;
                }
                else
                {
                    $message = 'Activate Request Rejected!';
                    $m = 'Activate Request Reject';
                    $active = 0;
                }

                // dd($message);
                foreach (Shop::whereIn('id', $ids)->get() as $key => $row) {
                    $user = User::find(Auth::id());
                    $user2 = User::find($row->status_user_id);

                    $msg = 'Shop (' .$row->company_name. ') '. $m .' by '.$user->name;
                    $user2->notify(new RequestNotification($user2 , $user , $msg , route('shop.index')));
                }

                Shop::whereIn('id', $ids)->update(['active' => $active , 'status_username' => Auth::user()->name ,  'status_user_id' => Auth::user()->id , 'remarks' => $request->remarks]);

            }

            DB::commit();
            return response()->json(['success' => $message]);
            // return response()->json(['success' => 'Deactivate  Request Submitted!']);
        } catch (Exception $th) {
            //throw $th;
            DB::rollback();
            return response()->json(['catchError' => $th->getMessage()]);
        }
    }

    public function get_shop_by_route(Request $request)
    {
        $route_id = $request->route_id;
        $shop = Shop::status()->where('route_id', $route_id)->get();
        return  response()->json(['shop' => $shop]);
    }

    public function get_shop_by_tso_old(Request $request)
    {
        $tso_id = $request->tso_id;


        $distributor_id = $request->distributor_id;
        $shop = Shop::status()->where('tso_id', $tso_id)->where('distributor_id',$distributor_id)->get();
        return  response()->json(['shop' => $shop]);
    }

public function get_shop_by_tso(Request $request)
{
    $tso_id = $request->tso_id;
    $distributor_id = $request->distributor_id;

    $shop = Shop::status()
        ->join('shop_tso', 'shops.id', '=', 'shop_tso.shop_id') // join with shop_tso
        ->where('shop_tso.tso_id', $tso_id) // tso filter from pivot table
        ->where('shops.distributor_id', $distributor_id) // distributor filter from shops table
        ->select('shops.*') // select only shops columns
        ->get();

    return response()->json(['shop' => $shop]);
}


    public function shopVisitList(Request $request)
    {

        if ($request->ajax()):

            $user_id =   TSO::find($request->tso_id)->user->id;

            $shopVisit = ShopVisit::with('shop:id,company_name,shop_code')
            ->where('shop_visits.user_id',$user_id)
            ->where('type',$request->type)
            ->whereBetween('visit_date', [$request->from, $request->to])
           ->get();
            return  view($this->page . 'shopVisit.ShopVisitListAjax',compact('shopVisit'));
        endif;
        return  view($this->page . 'shopVisit.ShopVisitList');
    }



    public function ImportShop(Request $request)
    {
        return  view($this->page . 'ImportShop');
    }

    public function import_shops_store_old(Request $request)
    {
        DB::beginTransaction();
        try {
            // dd($request->file('file'));
            $files = Excel::toArray([], $request->file('file'));
            // dd($files);
            // $file = $files[0];
            $shopExistis = [];
            $distributorNotExist = [];
            $tsoNotExist = [];
            $formatNotMatch = [];

            // dd($file , $file[0][1] , trim("SHOP NAME"));
            foreach ($files as $key1 => $file) {
                if($file[0][0] == trim("Distributor Code") && $file[0][1] == trim("Distributor (Company Name)") && $file[0][2] == trim("TSO Code") && $file[0][3] == trim("TSO") && $file[0][4] == trim("Route") && $file[0][5] == trim("Sub Route") && $file[0][6] == trim("Route day") && $file[0][7] == trim("Shop Type")
                && $file[0][8] == trim("Custom Code") && $file[0][9] == trim("Shop Name") && $file[0][10] == trim("Email") && $file[0][11] == trim("Phone") && $file[0][12] == trim("Mobile") && $file[0][14] == trim("City") && $file[0][15] == trim("State")  && $file[0][17] == trim("CNIC") && $file[0][22] == trim("Latitude") && $file[0][23] == trim("Longitude") ){
                    // if($file[0][1] == trim("SHOP NAME") && $file[0][2] == trim("City") && $file[0][3] == trim("State") && $file[0][6] == trim("Route") && $file[0][7] == trim("Day")){
                    foreach ($file as $key => $value) {


                        if($key == 0) continue ;
                        if($value[3] == null) continue ;
                        // $tso_id = $request->tso_id;
                        $tso_id = TSO::where('tso_code',$value[2])->value('id');
                        $distributor_id = Distributor::where('distributor_code',$value[0])->value('id');
                        // dd(ucfirst(trim($value[12])) , $value[12]);
                        $shop_data = Shop::where('company_name',ucfirst(trim($value[9])))
                        ->where('mobile_no' , ucfirst(trim($value[12])) )
                        ->where('address' , ucfirst(trim($value[13])))
                        ->where('distributor_id',$distributor_id)->where('tso_id', $tso_id)->first();

                        $distributor_id = Distributor::where('distributor_code',$value[0])->value('id');
                        // dd($distributor_id , $value[0]);
                        // if($distributor_id == null || $tso_id == null) continue ;
                        if ($distributor_id == null || $tso_id == null) {
                            array_push($distributorNotExist,$value[0]);
                            array_push($tsoNotExist,$value[2]);
                            continue;
                        }
                        // dd($file , $file[0][1] , trim("SHOP NAME"));
                        if(empty($shop_data)):
                            $route_data = Route::where('route_name',strtolower(trim($value[4])))->where('distributor_id',$distributor_id)->where('tso_id',$tso_id)->first();

                            if (empty($route_data)):

                                $route = new Route();
                                $route->tso_id = $tso_id;
                                $route->distributor_id =  $distributor_id;
                                $route->route_name = ucfirst(trim($value[4]));
                                $route->day = trim(ucfirst(($value[6])));
                                $route->save();
                                $route_id = $route->id;

                                RouteDay::create(['route_id'=>$route_id,'day'=>trim(ucfirst(($value[6])))]);
                            else:
                                $route_id = $route_data->id;
                                $matchThese = ['route_id'=>$route_id,'day'=>trim(ucfirst(($value[6])))];
                                RouteDay::updateOrCreate($matchThese,['day'=> trim(ucfirst(($value[6]))) ]);
                            endif;

                            $sub_route_data = SubRoutes::where('name',strtolower(trim($value[5])))->where('route_id',$route_id)->first();


                            if (empty($sub_route_data)):

                                $sub_route = new SubRoutes();
                                $sub_route->name = ucfirst(trim($value[5]));
                                $sub_route->route_id = $route_id;
                                $sub_route->save();
                                $sub_route_id = $sub_route->id;


                            else:
                                $sub_route_id = $sub_route_data->id;
                            endif;
                            $city_id =  City::where('name',$value[14])->value('id');
                            $shop = new Shop();
                            $shop->company_name = trim(ucfirst($value[9]));
                            $shop->shop_code = $shop->UniqueNo();
                            $shop->email =  $value[10];
                            $shop->city =  $city_id;
                            $shop->state = ucfirst($value[15]);
                            $shop->distributor_id = $distributor_id;
                            $shop->tso_id = $tso_id;
                            $shop->route_id = $route_id;
                            $shop->contact_person = $value[18];
                            $shop->phone = $value[11];
                            $shop->mobile_no = $value[12];
                            $shop->address = $value[13];
                            $shop->class = $value[25];
                            $shop->cnic = $value[17];
                            $shop->allow_credit_days = $value[19];
                            $shop->allow_credit_amount = $value[20];
                            $shop->delvery_days = $value[21];
                            $shop->sub_route_id = $sub_route_id;
                            $shop->latitude = $value[22];;
                            $shop->longitude = $value[23];;
                            $shop->location_radius = $value[24];;

                            $shop->save();


                        else:
                            array_push($shopExistis,$value[9]);


                        endif;


                    }
                    // DB::commit();
                    // if($shopExistis){Session::flash('exists',$shopExistis);}
                    // if($distributorNotExist){Session::flash('distriNotExist',$distributorNotExist);}
                    // if($tsoNotExist){Session::flash('tsoNotExist',$tsoNotExist);}

                    // return redirect()->back();
                }
                else{
                    // return redirect()->back()->with('catchError',"Format Not Match");
                    $sheet = 'Sheet'. ++$key1;
                    array_push($formatNotMatch,$sheet);
                }
            }

            DB::commit();
            if($shopExistis){Session::flash('exists',$shopExistis);}
            if($distributorNotExist){Session::flash('distriNotExist',$distributorNotExist);}
            if($tsoNotExist){Session::flash('tsoNotExist',$tsoNotExist);}
            if($formatNotMatch){Session::flash('formatNotMatch',$formatNotMatch);}
            return redirect()->back();

        } catch (Exception $th) {
            //throw $th;
            DB::rollback();
            dd($th->getMessage());
            return response()->json(['catchError' => $th->getMessage()]);
        }

    }

public function import_shops_store_old_18_08_25(Request $request)
{
    ini_set('max_execution_time', 0);
    ini_set('memory_limit', -1);

    DB::beginTransaction();

    try {
        $files = Excel::toArray([], $request->file('file'));

        $invalidRows = [];
        $shopExistsList = [];
        $formatNotMatch = [];

        foreach ($files as $sheetIndex => $file) {
            if (!isset($file[0][0]) || $file[0][0] !== "Distributor Code") {
                $formatNotMatch[] = 'Sheet' . ($sheetIndex + 1);
                continue;
            }

            foreach ($file as $key => $value) {
                if ($key === 0 || empty($value[9])) continue; // Skip header or missing Shop Name

                $rowNumber = $key + 1;

                $distributor_code = $value[0] ?? '';
                $tso_code_raw = str_replace(' ', '', $value[2] ?? '');
                $tso_codes = explode(',', $tso_code_raw);
                $shop_code = trim($value[8] ?? '');

                $distributor_id = Distributor::where('distributor_code', $distributor_code)->value('id');
                $valid_tso_ids = TSO::whereIn('tso_code', $tso_codes)->pluck('id')->toArray();

                if (!$distributor_id || count($valid_tso_ids) !== count($tso_codes)) {
                    $invalidRows[] = [
                        'row' => $rowNumber,
                        'distributor_code' => $distributor_code,
                        'tso_codes' => $tso_code_raw,
                        'message' => 'TSO(s) do not belong to the given distributor or distributor not found.'
                    ];
                    continue;
                }

                // ✅ Check only by shop_code now
                $shopExists = Shop::where('shop_code', $shop_code)->first();
                if ($shopExists) {
                    $shopExistsList[] = $shop_code;
                    continue;
                }

                // ✅ Get or create Route (by route_name and distributor_id only)
                $route = Route::firstOrCreate(
                    [
                        'route_name' => strtolower(trim($value[4] ?? '')),
                        'distributor_id' => $distributor_id
                    ],
                    [
                        'username' => optional(Auth::user())->name ?? 'Imported'
                    ]
                );

                // ✅ Create route_tso mapping (skip shop_id)
                foreach ($valid_tso_ids as $tsoId) {
                    RouteTso::updateOrCreate([
                        'route_id' => $route->id,
                        'tso_id' => $tsoId
                    ]);
                }

                // ✅ Create RouteDay
                if (!empty($value[6])) {
                    RouteDay::updateOrCreate(
                        [
                            'route_id' => $route->id,
                            'day' => ucfirst(trim($value[6]))
                        ],
                        ['day' => ucfirst(trim($value[6]))]
                    );
                }

                // ✅ Create or get SubRoute
                $sub_route = SubRoutes::firstOrCreate([
                    'name' => ucfirst(trim($value[5] ?? '')),
                    'route_id' => $route->id
                ]);

                $city_id = City::where('name', trim($value[14] ?? ''))->value('id');

                // ✅ Create Shop
                $shop = new Shop();
                $shop->company_name = ucfirst(trim($value[9] ?? ''));
                $shop->shop_code = $shop_code;
                $shop->email = $value[10] ?? '';
                $shop->city = $city_id;
                $shop->state = ucfirst($value[15] ?? '');
                $shop->distributor_id = $distributor_id;
                $shop->route_id = $route->id;
                $shop->contact_person = $value[18] ?? '';
                $shop->phone = $value[11] ?? '';
                $shop->mobile_no = trim($value[12] ?? '');
                $shop->address = ucfirst(trim($value[13] ?? ''));
                $shop->class = $value[25] ?? null;
                $shop->cnic = $value[17] ?? '';
                $shop->allow_credit_days = $value[19] ?? 0;
                $shop->allow_credit_amount = $value[20] ?? 0;
                $shop->delvery_days = $value[21] ?? 0;
                $shop->sub_route_id = $sub_route->id;
                $shop->latitude = $value[22] ?? null;
                $shop->longitude = $value[23] ?? null;
                $shop->location_radius = $value[24] ?? null;
                $shop->save();

                // ✅ Insert into shop_tso
                foreach ($valid_tso_ids as $tsoId) {
                    DB::table('shop_tso')->updateOrInsert([
                        'shop_id' => $shop->id,
                        'tso_id' => $tsoId
                    ]);
                }
            }
        }

        // ❌ Rollback if format/validation issues
        if (count($invalidRows) > 0 || count($formatNotMatch) > 0) {
            DB::rollBack();
            if ($shopExistsList) Session::flash('exists_count', count($shopExistsList));
            if ($invalidRows) Session::flash('invalid_rows', $invalidRows);
            if ($formatNotMatch) Session::flash('formatNotMatch', $formatNotMatch);
            return redirect()->back();
        }

        DB::commit();

        if ($shopExistsList) Session::flash('exists_count', count($shopExistsList));
        return redirect()->back()->with('success', 'All shops imported successfully.');

    } catch (\Throwable $th) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Import failed: ' . $th->getMessage());
    }
}

public function import_shops_store(Request $request)
{
    ini_set('max_execution_time', 0);
    ini_set('memory_limit', -1);

    DB::beginTransaction();

    try {
        $files = Excel::toArray([], $request->file('file'));

        $invalidRows = [];
        $shopExistsList = [];
        $formatNotMatch = [];
        $insertedCount = 0; // ✅ Counter for inserted shops

        foreach ($files as $sheetIndex => $file) {
            // ✅ Check format
            if (!isset($file[0][0]) || $file[0][0] !== "Distributor Code") {
                $formatNotMatch[] = 'Sheet' . ($sheetIndex + 1);
                dump("⚠️ Format not matched on Sheet " . ($sheetIndex + 1));
                continue;
            }

            foreach ($file as $key => $value) {
                if ($key === 0 || empty($value[9])) {
                    dump("⏭️ Row skipped: " . ($key + 1));
                    continue; // Skip header or missing Shop Name
                }

                $rowNumber = $key + 1;
                dump("➡️ Processing Row: $rowNumber");

                $distributor_code = $value[0] ?? '';
                $tso_code_raw = str_replace(' ', '', $value[2] ?? '');
                $tso_codes = explode(',', $tso_code_raw);
                $shop_code = trim($value[8] ?? '');

                $distributor_id = Distributor::where('distributor_code', $distributor_code)->value('id');
                $valid_tso_ids = TSO::whereIn('tso_code', $tso_codes)->pluck('id')->toArray();

                if (!$distributor_id || count($valid_tso_ids) !== count($tso_codes)) {
                    $invalidRows[] = [
                        'row' => $rowNumber,
                        'distributor_code' => $distributor_code,
                        'tso_codes' => $tso_code_raw,
                        'message' => 'TSO(s) do not belong to the given distributor or distributor not found.'
                    ];
                    dump("❌ Invalid Row: $rowNumber Distributor=$distributor_code TSO=$tso_code_raw");
                    continue;
                }

               if (empty($shop_code)) {
    $shop_code = Shop::UniqueNo();
}
                $shopExists = Shop::where('shop_code', $shop_code)->first();
                if ($shopExists) {
                    $shopExistsList[] = $shop_code;
                    dump("⚠️ Shop already exists: $shop_code (Row $rowNumber)");
                    continue;
                }

                // ✅ Get or create Route
                $route = Route::firstOrCreate(
                    [
                        'route_name' => strtolower(trim($value[4] ?? '')),
                        'distributor_id' => $distributor_id
                    ],
                    [
                        'username' => optional(Auth::user())->name ?? 'Imported'
                    ]
                );
                dump("✅ Route ID: " . $route->id);

                // ✅ Create route_tso mapping
                foreach ($valid_tso_ids as $tsoId) {
                    RouteTso::updateOrCreate([
                        'route_id' => $route->id,
                        'tso_id' => $tsoId
                    ]);
                }

                // ✅ Create RouteDay
                if (!empty($value[6])) {
                    RouteDay::updateOrCreate(
                        [
                            'route_id' => $route->id,
                            'day' => ucfirst(trim($value[6]))
                        ],
                        ['day' => ucfirst(trim($value[6]))]
                    );
                }

                // ✅ Create or get SubRoute
                $sub_route = SubRoutes::firstOrCreate([
                    'name' => ucfirst(trim($value[5] ?? '')),
                    'route_id' => $route->id
                ]);

                $city_id = City::where('name', trim($value[14] ?? ''))->value('id');
              

                // ✅ Create Shop
                $shop = new Shop();
                $shop->company_name = ucfirst(trim($value[9] ?? ''));
                $shop->shop_code = $shop_code;
                $shop->email = $value[10] ?? '';
                $shop->city = $city_id;
                $shop->state = ucfirst($value[15] ?? '');
                $shop->distributor_id = $distributor_id;
                $shop->route_id = $route->id;
                $shop->contact_person = $value[18] ?? '';
                $shop->phone = $value[11] ?? '';
                $shop->mobile_no = trim($value[12] ?? '');
                $shop->address = ucfirst(trim($value[13] ?? ''));
                $shop->class = $value[25] ?? null;
                $shop->cnic = $value[17] ?? '';
                $shop->allow_credit_days = $value[19] ?? 0;
                $shop->allow_credit_amount = $value[20] ?? 0;
                $shop->delvery_days = $value[21] ?? 0;
                $shop->sub_route_id = $sub_route->id;
                $shop->latitude = $value[22] ?? null;
                $shop->longitude = $value[23] ?? null;
                $shop->location_radius = $value[24] ?? null;
                $shop->save();

                dump("✅ Inserted Shop ID: " . $shop->id . " Code: " . $shop->shop_code);
                $insertedCount++;

                // ✅ Insert into shop_tso
                foreach ($valid_tso_ids as $tsoId) {
                    DB::table('shop_tso')->updateOrInsert([
                        'shop_id' => $shop->id,
                        'tso_id' => $tsoId
                    ]);
                }
            }
        }

        // ❌ Rollback if format/validation issues
        if (count($invalidRows) > 0 || count($formatNotMatch) > 0) {
            DB::rollBack();
            dump("🚨 Transaction Rolled Back! Invalid rows: " . count($invalidRows) . " Format issues: " . count($formatNotMatch));
            if ($shopExistsList) Session::flash('exists_count', count($shopExistsList));
            if ($invalidRows) Session::flash('invalid_rows', $invalidRows);
            if ($formatNotMatch) Session::flash('formatNotMatch', $formatNotMatch);
            return redirect()->back();
        }

        DB::commit();
        dump("🎉 Transaction Committed. Inserted Shops: $insertedCount");

        if ($shopExistsList) Session::flash('exists_count', count($shopExistsList));
      return redirect()->back()->with('success', "All shops imported successfully. Inserted: $insertedCount");

    } catch (\Throwable $th) {
        DB::rollBack();
        dd("❌ Error: " . $th->getMessage(), $th->getTraceAsString());
    }
}



}
