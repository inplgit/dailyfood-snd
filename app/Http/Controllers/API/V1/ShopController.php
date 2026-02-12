<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\ShopType;
use App\Models\ShopVisit;
use App\Models\User;
use App\Models\Route;
use App\Helpers\MasterFormsHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendSmsJob;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;
use App\Models\TSO;
use App\Models\ShopAttendence;
use App\Models\Rack;

use App\Models\Stock;
use App\Models\Product;
use App\Models\SaleOrder;
use App\Models\TSOTarget;
use App\Models\AssignRack;
use App\Models\Attendence;
use App\Models\Distributor;
use App\Models\SalesReturn;
use App\Models\ProductPrice;

use App\Models\SaleOrderData;
use App\Models\ReceiptVoucher;
use Yajra\DataTables\DataTables;



use Illuminate\Support\Facades\Storage;



use Illuminate\Support\Facades\Cache;


class ShopController extends BaseController
{public function shop_wise_sales_report(Request $request)
{
    $tso_id = $request->tso_id;
    $distributor_id = $request->distributor_id;
    $city = $request->city;
    $from = $request->from;
    $to = $request->to;

    $data = DB::table('sale_orders as a')
        ->leftJoin('sale_order_data as d', 'd.so_id', 'a.id')
        ->join('shops', 'shops.id', 'a.shop_id')
        ->join('products', 'd.product_id', 'products.id')
        ->join('routes', function ($join) use ($request) {
            $join->on('routes.id', '=', 'shops.route_id');
            if ($request->route_id != null)
                $join->where('routes.id', $request->route_id);
        })
        ->join('distributors as b', 'a.distributor_id', 'b.id')
        ->join('tso as c', function ($join) use ($request) {
            $join->on('c.id', '=', 'a.tso_id')->where('c.active', 1);
            if ($request->city != null)
                $join->where('c.city', $request->city);
        })
        ->leftJoin('users', 'users.id', '=', 'c.manager')
        ->leftJoin('users_distributors', 'c.user_id', '=', 'users_distributors.user_id')
        ->when($request->distributor_id == null, function ($query) use ($request) {
            $query->whereIn('users_distributors.distributor_id', MasterFormsHelper::get_users_distributors(Auth::user()->id));
        })
        ->join('cities', 'cities.id', 'c.city')
        ->when($request->distributor_id != null, function ($query) use ($request) {
            $query->where('a.distributor_id', $request->distributor_id);
        })
        ->when($request->tso_id != null, function ($query) use ($request) {
            $query->where('a.tso_id', $request->tso_id);
        })
        ->when($request->shop_id != null, function ($query) use ($request) {
            $query->where('a.shop_id', $request->shop_id);
        })
        ->when($request->product_id != null, function ($query) use ($request) {
            $query->where('d.product_id', $request->product_id);
        })
        ->when($request->execution != null, function ($query) use ($request) {
            $query->where('a.excecution', $request->execution);
        })
        ->whereBetween('a.dc_date', [$from, $to])
        ->where('a.status', 1)
        ->where('a.excecution', 1)
        ->where('c.status', 1)
        ->select(
            'a.id',
            'a.distributor_id',
            'a.tso_id',
            'shops.id as shop_id',
            'shops.shop_code',
            'shops.company_name as shop_name',
            'routes.route_name',
            'b.distributor_name',
            'c.name as tso',
            'users.name as manager',
            'cities.name as city',
            DB::raw('SUM(a.total_pcs) as qty'),
            DB::raw('SUM(a.total_amount) as rate'),
            DB::raw('SUM(a.total_amount) as net_sales')
        )
        ->groupBy(
            'shops.id',
            'shops.shop_code',
            'shops.company_name',
            'routes.route_name',
            'b.distributor_name',
            'c.name',
            'users.name',
            'cities.name'
        )
        ->orderBy('shops.shop_code')
        ->orderBy('products.orderby', 'ASC')
        ->get();

    // Agar returned_qty bhi API me bhejna hai to yahin add kar do:
    foreach ($data as $row) {
        $row->sale_return = MasterFormsHelper::get_returned_qty_by_sale_order_id(
            $row->distributor_id,
            $row->tso_id,
            $row->shop_id,
            $from,
            $to
        );
    }

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}



    public function update_location(Request $request)
    {

      
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
    
        $shop = Shop::find($request->shop_id);
    
        if (!$shop) {
            return response()->json([
                'message' => 'Shop not found.',
            ], 404);
        }
    
        $shop->latitude = $request->latitude;
        $shop->longitude = $request->longitude;
        $shop->save();
    
        return response()->json([
            'message' => 'location updated successfully.',
            'success' => true
        ]);
    }

public function order_booker_daily_activity_location_report_new(Request $request)
{
    $from           = $request->from;
    $to             = $request->to;
    $distributor_id = $request->distributor_id;
    $tso_id         = $request->tso_id;
    $city           = $request->city;
    $shop_id        = $request->shop_id;
    $route_id       = $request->route_id;

    // fetch shops with basic info
    $data = DB::table('shops as a')
        ->join('routes', 'routes.id', '=', 'a.route_id')
        ->join('distributors as b', 'a.distributor_id', '=', 'b.id')
        ->join('shop_tso as st', function ($join) use ($tso_id) {
            $join->on('st.shop_id', '=', 'a.id');
            if ($tso_id) {
                $join->where('st.tso_id', $tso_id);
            }
        })
        ->join('tso as c', 'c.id', '=', 'st.tso_id')
        ->join('users as d', 'd.id', '=', 'c.manager')
        ->when($distributor_id, fn($q) => $q->where('a.distributor_id', $distributor_id))
        ->when($shop_id, fn($q) => $q->where('a.id', $shop_id))
        ->when($route_id, fn($q) => $q->where('a.route_id', $route_id))
        ->select(
            'a.id',
            'a.shop_code',
            'a.company_name as shop_name',
            'a.distributor_id',
            'a.latitude',
            'a.longitude',
            'b.distributor_name',
            'c.id as tso_id',
            'c.name as tso',
            'c.user_id',
            'd.name as manager_name'
        )
        ->where('a.status', 1)
        ->get();

    if ($data->isEmpty()) {
        return response()->json(['success' => true, 'data' => []]);
    }

    $shopIds = $data->pluck('id')->toArray();

    // ✅ fetch all sale orders in bulk (NO foreach query)
    // $allSaleOrders = DB::table('sale_orders')
    //     ->whereIn('shop_id', $shopIds)
    //     ->whereBetween('dc_date', [$from, $to])
    //     ->get()
    //     ->groupBy('shop_id');
    $allSaleOrders = DB::table('sale_orders as so')
        ->leftJoin('users_locations as ul', function ($join) {
            $join->on('ul.user_id', '=', 'so.user_id')
                ->whereRaw('DATE(ul.created_at) = DATE(so.created_at)');
        })
        ->whereIn('so.shop_id', $shopIds)
        ->whereBetween('so.dc_date', [$from, $to])
        ->select(
            'so.*',
            'ul.latitude as user_latitude',
            'ul.longitude as user_longitude'
        )
        ->get()
        ->groupBy('shop_id');

    // ✅ fetch all visits in bulk (NO foreach query)
    $allVisits = DB::table('shop_visits')
        ->whereIn('shop_id', $shopIds)
        ->whereBetween('visit_date', [$from, $to])
        ->get()
        ->groupBy('shop_id');

    // visit reasons
    $statuses = [
        0 => '',
        1 => 'Stock Available',
        2 => 'No Sale',
        3 => 'Owner Not Available',
        4 => 'Shop Closed',
    ];

    $response = [];

    foreach ($data as $row) {

        $saleOrders = $allSaleOrders[$row->id] ?? collect();
        $visits     = $allVisits[$row->id] ?? collect();

        $total_pcs = $saleOrders->sum('total_pcs') ?? 0;
        $unit_record = $saleOrders->first();

        // PRODUCTIVE row
        if ($unit_record) {
            $lat = $unit_record->user_latitude ?? $row->latitude;
            $long = $unit_record->user_longitude ?? $row->longitude;
            $response[] = [
                'shop_code'   => $row->shop_code,
                'tso'         => $row->tso,
                'manager'     => $row->manager_name,
                'distributor' => $row->distributor_name,
                'shop_name'   => $row->shop_name,
                'status'      => 'Productive Shop',
                'pcs'         => $total_pcs,
                // 'map'         => ($lat && $long) ? "https://www.google.com/maps?q={$lat},{$long}" : null,
                'map'         => ($lat && $long) ? MasterFormsHelper::getAddress($lat, $long) : null,
                'latitude'    => $lat,
                'longitude'   => $long,
                'date'        => $unit_record->dc_date ?? $unit_record->created_at,
                'time'        => $unit_record->created_at,
            ];
        }

        // UNPRODUCTIVE rows
        foreach ($visits as $visit) {
            $lat = $visit->latitude ?? $row->latitude;
            $long = $visit->longitude ?? $row->longitude;
            $response[] = [
                'shop_code'   => $row->shop_code,
                'tso'         => $row->tso,
                'manager'     => $row->manager_name,
                'distributor' => $row->distributor_name,
                'shop_name'   => $row->shop_name,
                'status'      => 'Unproductive Shop',
                'reason'      => $statuses[$visit->visit_reason_id ?? 0] ?? '',
                'pcs'         => 0,
                // 'map'         => ($lat && $long) ? "https://www.google.com/maps?q={$lat},{$long}" : null,
                'map'         => ($lat && $long) ? MasterFormsHelper::getAddress($lat, $long) : null,
                'latitude'    => $lat,
                'longitude'   => $long,
                'date'        => $visit->visit_date,
                'time'        => $visit->created_at,
            ];
        }
    }

    return response()->json([
        'success' => true,
        'data'    => $response,
    ]);
}

public function order_booker_daily_activity_location_report(Request $request)
{
    $from = $request->from;
    $to = $request->to;
    $distributor_id = $request->distributor_id;
    $tso_id = $request->tso_id;
    $city = $request->city;
    $shop_id = $request->shop_id;
    $route_id = $request->route_id;
    
    // aggregated orders subquery
    $ordersAgg = DB::raw("
        (
            SELECT
                e.shop_id,
                DATE(e.created_at) AS order_date,
                COUNT(DISTINCT e.id) AS productives,
                SUM(sod.qty) AS executed_qty,
                SUM(sod.rate * sod.qty) AS executed_sales,
                COALESCE(sr.total_return_qty, 0) AS shop_with_return
            FROM sale_orders e
            JOIN sale_order_data sod ON sod.so_id = e.id
            LEFT JOIN (
                SELECT shop_id, SUM(quantity) AS total_return_qty
                FROM sale_order_return_details
                WHERE status = 1 AND excecution = 1
                GROUP BY shop_id
            ) sr ON sr.shop_id = e.shop_id
            WHERE e.status = 1 AND e.excecution = 1
            GROUP BY e.shop_id, DATE(e.created_at), sr.total_return_qty
        ) ord
    ");

    // tso ids by distributor
    $tsoIds = null;
    if ($distributor_id && !$tso_id) {
        $tsoIds = DB::table('tso')
            ->where('distributor_id', $distributor_id)
            ->pluck('id')
            ->toArray();
    }

    // main query
    $data = DB::table('shops as a')
        ->leftJoin('shop_visits as sv', function ($join) use ($from, $to, $request) {
            $join->on('a.id', '=', 'sv.shop_id')
                 ->where('sv.type', 0);

            if ($from && $to) {
                $join->whereBetween('sv.visit_date', [$from, $to]);
            }
            if ($request->visit_date) {
                $join->where('sv.visit_date', $request->visit_date);
            }
        })
        ->join('routes', 'routes.id', '=', 'a.route_id')
        ->join('distributors as b', 'a.distributor_id', '=', 'b.id')
        ->join('shop_tso as st', function ($join) use ($tso_id) {
            $join->on('st.shop_id', '=', 'a.id');
            if ($tso_id) {
                $join->where('st.tso_id', $tso_id);
            }
        })
        ->join('tso as c', 'c.id', '=', 'st.tso_id')
        ->join('users as d', 'd.id', '=', 'c.manager')
        ->leftJoin($ordersAgg, function ($join) {
            $join->on('a.id', '=', 'ord.shop_id')
                 ->on('sv.visit_date', '=', 'ord.order_date');
        })
        ->when($distributor_id, fn($q) => $q->where('a.distributor_id', $distributor_id))
        ->when($shop_id, fn($q) => $q->where('a.id', $shop_id))
        ->when($tsoIds, fn($q) => $q->whereIn('st.tso_id', $tsoIds))
        ->when($route_id, fn($q) => $q->where('a.route_id', $route_id))
        ->where('a.status', 1)
        ->select(
            'a.id',
            'a.distributor_id',
            'b.distributor_name',
            'c.name as tso',
            'c.id as tso_id',
            'c.user_id',
            'd.name as manager_name',
            'sv.visit_date',
            'sv.created_at as visit_created_at',
            'sv.latitude as visit_latitude',
            'sv.longitude as visit_longitude',
            'a.shop_code',
            'a.company_name as shop_name',
            'a.remarks as shop_remarks',
            'a.map as shop_map_name',
            'a.latitude',
            'a.longitude',
            'routes.route_name',
            DB::raw('DATE_FORMAT(a.created_at, "%Y-%m-%d") as shop_date'),
            DB::raw('DATE_FORMAT(a.created_at, "%H:%i:%s") as shop_time'),
            DB::raw('COUNT(DISTINCT sv.id) as total_visit'),
            DB::raw('COALESCE(MAX(ord.productives), 0) as productive_visit'),
            DB::raw('COALESCE(MAX(ord.executed_qty), 0) as executed_qty'),
            DB::raw('COALESCE(MAX(ord.executed_sales), 0) as executed_sales'),
            DB::raw('COALESCE(MAX(ord.shop_with_return), 0) as shop_with_return')
        )
        ->groupBy('a.id', 'b.distributor_name', 'c.name', 'c.id')
        ->orderBy('a.id', 'ASC')
        ->get();

    $saleOrdersAll = SaleOrder::with(['usersLocation' => function ($q) {
        $q->latest();
    }])
    ->whereBetween('dc_date', [$from, $to])
    ->whereIn('shop_id', $data->pluck('id'))
    ->get()
    ->each(function ($order) {
        $matched = $order->usersLocation->firstWhere('created_at', $order->created_at)
            ?? $order->usersLocation->firstWhere('created_at', $order->updated_at);

        $order->setRelation('usersLocation', $matched);
    })
    ->groupBy(function ($order) {
        return $order->shop_id . '|' . $order->tso_id . '|' . $order->distributor_id;
    });

    $allShopVisits = DB::table('shop_visits')
        ->whereBetween('visit_date', [$from, $to])
        ->whereIn('shop_id', $data->pluck('id'))
        ->whereIn('user_id', $data->pluck('user_id'))
        ->get()
        ->groupBy(function ($visit) {
            return $visit->shop_id . '|' . $visit->user_id;
        });

    $prepared = $data->map(function ($row) use ($saleOrdersAll, $allShopVisits) {
        $key = $row->id . '|' . $row->tso_id . '|' . $row->distributor_id;
        $saleOrders = $saleOrdersAll->get($key, collect());
        $row->total_pcs = $saleOrders->sum('total_pcs') ?? 0;
        $row->unit_record = $saleOrders->first();
        $visitKey = $row->id . '|' . $row->user_id;
        $row->shop_visits = $allShopVisits->get($visitKey, collect());
        return $row;
    });

    return response()->json([
        'success' => true,
        'data' => $prepared
    ]);
}


    public function addShop(Request $request)
{


     $validator = Validator::make($request->all(), [
        'contact_person' => 'required',
        'company_name'   => 'required',
        'mobile_no'      => 'required|unique:shops,mobile_no',
        'latitude'       => 'required',
        'longitude'      => 'required',
        'class'          => 'required',
   'route_id'       => 'required|exists:routes,id',
        'image'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);





    if ($validator->fails()) {
        // Convert all errors to a single string message
        $allErrors = implode(' | ', collect($validator->errors()->all())->toArray());

        return response()->json([
            'status' => false,
            'message' => $allErrors,  // All validation errors shown here
        ], 422);
    }

    DB::beginTransaction();
    try {
        // Get route and its TSOs from route_tso pivot
        $route = Route::findOrFail($request->route_id);
        $tsoIds = DB::table('route_tso')
                    ->where('route_id', $request->route_id)
                    ->pluck('tso_id')
                    ->toArray();

                 

        if (empty($tsoIds)) {
            return $this->sendError('No TSO assigned to this route.');
        }

        // Prepare shop base data
        $shopCode = Shop::UniqueNo();
        $mobile   = MasterFormsHelper::correctPhoneNumber($request->mobile_no);
        $fileName = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '-' . $file->getClientOriginalName();
            $file->storeAs('shop_image', $fileName, 'public');
        }

        // Create the shop
        $shop = Shop::create([
            'shop_code'       => $shopCode,
            'distributor_id'  => $route->distributor_id,
            'note'            => $request->note,
            'contact_person'  => $request->contact_person,
            'company_name'    => $request->company_name,
            'mobile_no'       => $mobile,
            'phone'           => $request->phone,
            'alt_phone'       => $request->alt_phone,
            'cnic'            => $request->cnic,
            'address'         => $request->address,
            'latitude'        => $request->latitude,
            'longitude'       => $request->longitude,
            'shop_type_id'    => $request->shop_type_id,
            'email'           => $request->email,
            'payment_mode'    => $request->payment_mode,
            'route_id'        => $request->route_id,
            'class'           => $request->class,
            'channel_id'      => $request->channel_id,
            'balance_amount'  => $request->balance_amount ?? 0,
            'debit_credit'    => $request->debit_credit ?? 1,
            'image'           => $fileName,
        ]);

  
       $shop->tsos()->sync($tsoIds);

      
        MasterFormsHelper::users_location_submit($shop, $request->latitude, $request->longitude, 'shops', 'Create Shop');

        DB::commit();
        return $this->sendResponse([], 'Shop added successfully.');
    } catch (\Exception $e) {
        DB::rollback();
        return $this->sendError('Server Error.', ['error' => $e->getMessage()]);
    }
}
    public function addShop_old(Request $request)
    {
        $request->validate([
            'contact_person' => 'required',
            'company_name' => 'required',
            'mobile_no' => 'required|unique:shops,mobile_no',
         //   'phone' => 'required',
        //    'alt_phone' => 'required',
        //    'cnic' => 'required',
        //    'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
       //     'shop_type_id' => 'required',
       //     'email' => 'required',
            // 'tso_id' => 'required',
        //    'payment_mode' => 'required',
        //    'note' => 'required',
            'class'=> 'required',
            'route_id'=> 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        DB::beginTransaction();
        try {

            $route_data=  Route::where('id',$request->route_id)->first();
            $request['shop_code'] = Shop::UniqueNo();
            $request['tso_id'] = $route_data->tso_id;

            $request['distributor_id'] = $route_data->distributor_id;
            $request['mobile_no'] = MasterFormsHelper::correctPhoneNumber($request['mobile_no']);
          

            $fileName = '';
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '-' . $file->getClientOriginalName();
                $file->storeAs('shop_image', $fileName, 'public');
            }
    
            $data = $request->only([
                'shop_code', 'distributor_id', 'tso_id', 'note', 'contact_person', 'company_name',
                'mobile_no', 'phone', 'alt_phone', 'cnic', 'address', 'latitude', 'longitude',
                'shop_type_id', 'email', 'payment_mode', 'route_id', 'class', 'balance_amount',
                'debit_credit',
            ]);
            $data['image'] = $fileName;

            $shop = Shop::create($data);
            MasterFormsHelper::users_location_submit($shop,$request->latitude,$request->longitude,'shops', 'Create Shop');

            // SendSmsJob::dispatch( $request['mobile_no'] , "Dear $request->contact_person,\n Welcome to Smile Food Pakistan");

            DB::commit();
            return $this->sendResponse([], 'Shop Add Successfully.');
        } catch (Exception $th) {
            DB::rollBack();
            return $this->sendError('Server Error.', ['error'=>$th->getMessage()]);
        }
    }


//    public function userWiseShopList(Request $request)
// {

//  $request->validate([
//         'route_id' => 'required|exists:routes,id'
//     ]);


//     $tsoId = Auth::user()->tso->id;
//     $today = date('Y-m-d');

//     $shops = Shop::join('shop_tso', 'shop_tso.shop_id', '=', 'shops.id')
//         ->leftJoin('sale_orders', function ($join) use ($today) {
//             $join->on('sale_orders.shop_id', '=', 'shops.id')
//                  ->whereDate('sale_orders.dc_date', $today);
//         })
//         ->leftJoin('shop_visits', function ($join) use ($today) {
//             $join->on('shop_visits.shop_id', '=', 'shops.id')
//                  ->whereDate('shop_visits.visit_date', $today);
//         })
//         ->leftJoin('shops_outstandings', 'shops_outstandings.shop_id', '=', 'shops.id')
//         ->where('shop_tso.tso_id', $tsoId)
// ->where('shops.route_id', $request->route_id) 
//  ->where('shops.status', 1)   // <-- Added
//     // ->where('shops.active', 1)   // <-- Added
//         ->when($request->search, function ($query) use ($request) {
//             $query->where('shops.company_name', 'like', '%' . $request->search . '%');
//         })
//         ->when($request->id, function ($query) use ($request) {
//             $query->where('shops.id', $request->id);
//         })
//         ->when($request->cat_id, function ($query) use ($request) {
//             $query->where('shops.shop_type_id', $request->cat_id);
//         })
//         ->select(
//             'shops.*',
//             DB::raw('
//                 CASE 
//                     WHEN sale_orders.id IS NOT NULL THEN 1 
//                     ELSE 0 
//                 END as productive
//             '),
//             DB::raw('
//                 CASE 
//                     WHEN shop_visits.id IS NOT NULL THEN 1 
//                     ELSE 0 
//                 END as visited
//             '),
//             DB::raw('(
//                 shops_outstandings.so_amount + 
//                 shops_outstandings.sr_amount +
//                 CASE
//                     WHEN shops.debit_credit = 1 THEN shops.balance_amount
//                     WHEN shops.debit_credit = 2 THEN -shops.balance_amount
//                     ELSE 0
//                 END
//                 - shops_outstandings.rv_amount
//             ) as outstandings')
//         )
//         ->distinct();

//     $shops = $shops->paginate($request->limit ?? 5);

//     return $this->sendResponse([$shops], 'Shop List Successfully Retrieved.');
// }


public function updateShop(Request $request, $id)
{
    $shop = Shop::find($id);

    if (!$shop) {
        return response()->json([
            'status' => false,
            'message' => 'Shop not found.'
        ], 404);
    }

    $validator = Validator::make($request->all(), [
        'contact_person' => 'required',
        'company_name'   => 'required',
        'mobile_no'      => 'required',
        'latitude'       => 'required',
        'longitude'      => 'required',
        'class'          => 'required',
        'route_id'       => 'required|exists:routes,id',
        'image'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($validator->fails()) {
        $allErrors = implode(' | ', collect($validator->errors()->all())->toArray());

        return response()->json([
            'status' => false,
            'message' => $allErrors,
        ], 422);
    }

    DB::beginTransaction();

    try {

        // Route and TSOs
        $route = Route::findOrFail($request->route_id);

        $tsoIds = DB::table('route_tso')
            ->where('route_id', $request->route_id)
            ->pluck('tso_id')
            ->toArray();

        if (empty($tsoIds)) {
            return $this->sendError('No TSO assigned to this route.');
        }

        // Mobile format
        $mobile = MasterFormsHelper::correctPhoneNumber($request->mobile_no);

        // Image update (optional)
        $fileName = $shop->image;

        if ($request->hasFile('image')) {

            // remove old image (optional)
            if ($shop->image && \Storage::disk('public')->exists('shop_image/' . $shop->image)) {
                \Storage::disk('public')->delete('shop_image/' . $shop->image);
            }

            $file = $request->file('image');
            $fileName = time() . '-' . $file->getClientOriginalName();
            $file->storeAs('shop_image', $fileName, 'public');
        }

        // Update shop
        $shop->update([
            'distributor_id'  => $route->distributor_id,
            'note'            => $request->note,
            'contact_person'  => $request->contact_person,
            'company_name'    => $request->company_name,
            'mobile_no'       => $mobile,
            'phone'           => $request->phone,
            'alt_phone'       => $request->alt_phone,
            'cnic'            => $request->cnic,
            'address'         => $request->address,
            'latitude'        => $request->latitude,
            'longitude'       => $request->longitude,
            'shop_type_id'    => $request->shop_type_id,
            'email'           => $request->email,
            'payment_mode'    => $request->payment_mode,
            'route_id'        => $request->route_id,
            'class'           => $request->class,
            'channel_id'      => $request->channel_id,
            'balance_amount'  => $request->balance_amount ?? 0,
            'debit_credit'    => $request->debit_credit ?? 1,
            'image'           => $fileName,
            'update_api'           => 1,
        ]);

        // Update TSOs
        $shop->tsos()->sync($tsoIds);

        MasterFormsHelper::users_location_submit($shop, $request->latitude, $request->longitude, 'shops', 'Update Shop');

        DB::commit();

        return $this->sendResponse([], 'Shop updated successfully.');

    } catch (\Exception $e) {
        DB::rollback();

        return $this->sendError('Server Error.', [
            'error' => $e->getMessage()
        ]);
    }
}

public function userWiseShopList(Request $request)
{
    $request->validate([
        'route_id' => 'required|exists:routes,id'
    ]);

    $tsoId = Auth::user()->tso->id;
    $distributor_id = Auth::user()->tso->distributor_id;
    $today = date('Y-m-d');

    $shops = Shop::join('shop_tso', 'shop_tso.shop_id', '=', 'shops.id')
        ->leftJoin('sale_orders', function ($join) use ($today) {
            $join->on('sale_orders.shop_id', '=', 'shops.id')
                 ->whereDate('sale_orders.dc_date', $today);
        })
        ->leftJoin('shop_visits', function ($join) use ($today) {
            $join->on('shop_visits.shop_id', '=', 'shops.id')
                 ->whereDate('shop_visits.visit_date', $today);
        })
        ->leftJoin('shops_outstandings', 'shops_outstandings.shop_id', '=', 'shops.id')
        ->where('shop_tso.tso_id', $tsoId)
        ->where('shops.route_id', $request->route_id)
        ->where('shops.distributor_id', $distributor_id)
        ->where('shops.status', 1)    // ✅ shop enabled
        ->where('shops.active', 1)    // ✅ active shop only
        ->when($request->search, function ($query) use ($request) {
            $query->where('shops.company_name', 'like', '%' . $request->search . '%');
        })
        ->when($request->id, function ($query) use ($request) {
            $query->where('shops.id', $request->id);
        })
        ->when($request->cat_id, function ($query) use ($request) {
            $query->where('shops.shop_type_id', $request->cat_id);
        })
        ->select(
            'shops.*',
            DB::raw('
                CASE 
                    WHEN sale_orders.id IS NOT NULL THEN 1 
                    ELSE 0 
                END as productive
            '),
            DB::raw('
                CASE 
                    WHEN shop_visits.id IS NOT NULL THEN 1 
                    ELSE 0 
                END as visited
            '),
            DB::raw('(
                shops_outstandings.so_amount + 
                shops_outstandings.sr_amount +
                CASE
                    WHEN shops.debit_credit = 1 THEN shops.balance_amount
                    WHEN shops.debit_credit = 2 THEN -shops.balance_amount
                    ELSE 0
                END
                - shops_outstandings.rv_amount
            ) as outstandings')
        )
        ->distinct();

    $shops = $shops->paginate($request->limit ?? 5);

    return $this->sendResponse([$shops], 'Shop List Successfully Retrieved.');
}



    public function shopTypeList()
    {
        return $this->sendResponse([ShopType::latest()->get()], 'Shop Type List Successfully Retrive.');
    }

    public function visitShopAdd(Request $request)
    {
           $validator = Validator::make($request->all(), [
            'shop_id' => 'required',

            'remark' => 'required',
            'visit_date' => 'required',
          //  'latitude' => 'required',
           // 'longitude' => 'required',
            // 'user_id'=> 'required',
        ]);

   if ($validator->fails()) {
        // Convert all errors to a single string message
        $allErrors = implode(' | ', collect($validator->errors()->all())->toArray());

        return response()->json([
            'status' => false,
            'message' => $allErrors,  // All validation errors shown here
        ], 422);
    }


        $request['user_id'] = Auth::id();

        $marchadising ='';
        // if ($request->file('merchandising_image')) {
        //     $file = $request->file('merchandising_image');
        //     $marchadising = time() . $file->getClientOriginalName();
        //     $file->storeAs('visitshope', $marchadising, 'public'); // 'uploads' is the directory to store files.
        // }


        if ($request->hasFile('merchandising_image')) {
    $file = $request->file('merchandising_image');

    // Unique filename with extension
    $marchadising = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

    // Save file to storage/app/public/visitshope
    $file->storeAs('visitshope', $marchadising, 'public');
}


        if(!empty($marchadising))
            {
               $marchadising = $marchadising;
            }
        $data =$request->only('user_id','shop_id','visit_reason_id','remark','visit_date','latitude','longitude','type');
        $data['merchandising_image'] = $marchadising;


if ($request->has('visit_time') && !empty($request->visit_time)) {
    $data['created_at'] = $request->visit_time;
}
        $visit= ShopVisit::create($data);
       MasterFormsHelper::users_location_submit($visit,$request->latitude,$request->longitude,'shop_visits', 'Shop Visit');
        return $this->sendResponse([], 'Shop Visit Successfully Inserted.');
    }

    public function visitShopList(Request $request)
    {
        $type = $request->type ?? 0;
        $shopVisit = ShopVisit::with('shop:id,company_name,shop_code')->where('shop_visits.user_id',Auth::id())
        ->where('type',$type)
        ->latest()->paginate($request->limit??5);
        return $this->sendResponse([$shopVisit], 'Shop Type List Successfully Retrive.');
    }

// public function visitShopAddBulk(Request $request)
// {
//     date_default_timezone_set("Asia/Karachi");

//     // Decode visits JSON from form-data
//     $visits = json_decode($request->visits, true);
//     $images = $request->file('merchandising_image', []);
//     $userId = Auth::id();

//     $response = [
//         'success' => [],
//         'failed'  => []
//     ];

//     foreach ($visits as $index => $visit) {

//         // Optional local_id for mobile sync
//         $visit['local_id'] = (string) ($visit['local_id'] ?? '');

//         // Validation
//         $validator = Validator::make($visit, [
//             'shop_id'    => 'required|exists:shops,id',
//             'remark'     => 'required',
//             'visit_date' => 'required|date',
//         ]);

//         if ($validator->fails()) {
//             $response['failed'][] = [
//                 'local_id' => $visit['local_id'],
//                 'errors'   => $validator->errors()->all()
//             ];
//             continue;
//         }

//         DB::beginTransaction();

//         try {
//             // Handle merchandising image for this visit
//             $merchandisingImage = null;
//             if (isset($images[$index])) {
//                 $file = $images[$index];
//                 $merchandisingImage = time() . "-{$index}-" . $file->getClientOriginalName();
//                 $file->storeAs('visitshope', $merchandisingImage, 'public');
//             }

//             // Prepare visit data
//             $data = [
//                 'user_id'             => $userId,
//                 'shop_id'             => $visit['shop_id'],
//                 'visit_reason_id'     => $visit['visit_reason_id'] ?? null,
//                 'remark'              => $visit['remark'],
//                 'visit_date'          => $visit['visit_date'],
//                 'latitude'            => $visit['latitude'] ?? null,
//                 'longitude'           => $visit['longitude'] ?? null,
//                 'type'                => $visit['type'] ?? null,
//                 'merchandising_image' => $merchandisingImage,
//                 'created_at'          => $visit['visit_time'] ?? now(),
//             ];

//             // Create visit
//             $shopVisit = ShopVisit::create($data);

//             // Save user location
//             MasterFormsHelper::users_location_submit(
//                 $shopVisit,
//                 $visit['latitude'] ?? null,
//                 $visit['longitude'] ?? null,
//                 'shop_visits',
//                 'Shop Visit'
//             );

//             DB::commit();

//             $response['success'][] = [
//                 'local_id' => $visit['local_id'],
//                 'visit_id' => $shopVisit->id
//             ];

//         } catch (\Exception $e) {
//             DB::rollBack();

//             $response['failed'][] = [
//                 'local_id' => $visit['local_id'],
//                 'error'    => $e->getMessage() . ' on line ' . $e->getLine()
//             ];
//         }
//     }

//     return response()->json($response);
// }

public function visitShopAddBulk(Request $request)
{
    date_default_timezone_set("Asia/Karachi");

    $visits = json_decode($request->visits, true);
    $images = $request->file('merchandising_image', []);
    $userId = Auth::id();
    
    $response = [
        'success' => [],
        'failed'  => []
    ];

    foreach ($visits as $index => $visit) {

        // force local_id as string
        $visit['local_id'] = (string) ($visit['local_id'] ?? '');

        /* ================= DUPLICATE LOCAL ID CHECK ================= */

        if (!empty($visit['local_id'])) {

            $existingVisit = ShopVisit::where('local_id', $visit['local_id'])->first();

            if ($existingVisit) {
                $response['success'][] = [
                    'local_id' => $visit['local_id'],
                    'visit_id' => $existingVisit->id,
                    'message'  => 'Visit with this local_id already exists'
                ];
                continue; // 🔥 skip insert
            }
        }

        /* ================= VALIDATION ================= */

        $validator = Validator::make($visit, [
            'shop_id'    => 'required|exists:shops,id',
            'remark'     => 'required',
            'visit_date' => 'required|date',
            'latitude'   => 'required',
            'longitude'  => 'required',
        ]);

        if ($validator->fails()) {
            $response['failed'][] = [
                'local_id' => $visit['local_id'],
                'errors'   => $validator->errors()->all()
            ];
            continue;
        }

        DB::beginTransaction();

        try {

            /* ================= IMAGE HANDLING ================= */

            $merchandisingImage = null;

            if (!empty($images[$index])) {
                $file = $images[$index];

                $merchandisingImage =
                    time() . "-{$index}-" . uniqid() . '.' . $file->getClientOriginalExtension();

                $file->storeAs('visitshope', $merchandisingImage, 'public');
            }

            /* ================= CREATE VISIT ================= */

            $shopVisit = ShopVisit::create([
                'user_id'             => $userId,
                'shop_id'             => $visit['shop_id'],
                'visit_reason_id'     => $visit['visit_reason_id'] ?? null,
                'remark'              => $visit['remark'],
                'visit_date'          => $visit['visit_date'],
                'latitude'            => $visit['latitude'],
                'longitude'           => $visit['longitude'],
                'type'                => $visit['type'] ?? null,
                'merchandising_image' => $merchandisingImage,

                // 🔥 local_id save
                'local_id'   => $visit['local_id'],
                'created_at' => $visit['visit_time'] ?? now(),
            ]);

            /* ================= LOCATION LOG ================= */

            MasterFormsHelper::users_location_submit(
                $shopVisit,
                $visit['latitude'],
                $visit['longitude'],
                'shop_visits',
                'Shop Visit'
            );

            DB::commit();

            $response['success'][] = [
                'local_id' => $visit['local_id'],
                'visit_id' => $shopVisit->id,
                'message'  => 'Visit inserted successfully'
            ];

        } catch (\Exception $e) {

            DB::rollBack();

            $response['failed'][] = [
                'local_id' => $visit['local_id'],
                'error'    => $e->getMessage() . ' line ' . $e->getLine()
            ];
        }
    }

    return response()->json($response);
}

public function api_shop_update(Request $request, Shop $shop)
{
    // Direct validation - only validate fields that are present in the request
    $validator = Validator::make($request->all(), [
        'shop_code' => 'nullable|string|max:50|unique:shops,shop_code,' . $shop->id,
        'custome_code' => 'nullable|string|max:50',
        'company_name' => 'nullable|string|max:255',
        'title' => 'nullable|string|max:255',
        'contact_person' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        'alt_email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'mobile_no' => 'nullable|string|max:20',
        'alt_phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
        'address_2' => 'nullable|string|max:500',
        'city' => 'nullable|integer|exists:cities,id',
        'state' => 'nullable|string|max:100',
        'zip_code' => 'nullable|string|max:20',
        'note' => 'nullable|string',
        'filer' => 'nullable|in:0,1',
        'cnic' => 'nullable|string|max:20',
        'allow_credit_days' => 'nullable|integer|min:0',
        'allow_credit_amount' => 'nullable|numeric|min:0',
        'delvery_days' => 'nullable|integer|min:0',
        'invoice_discount' => 'nullable|numeric|min:0|max:100',
        'shop_type_id' => 'nullable|integer|exists:shop_types,id',
        'channel_id' => 'nullable|integer|exists:shop_channels,id',
        'shop_zone_id' => 'nullable|integer|exists:zones,id',
        'balance_amount' => 'nullable|numeric|min:0',
        'debit_credit' => 'nullable|in:1,2',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'shop_location' => 'nullable|in:0,1',
        'map' => 'nullable|string|max:255',
        'latitude' => 'nullable|numeric|between:-90,90',
        'longitude' => 'nullable|numeric|between:-180,180',
        'location_radius' => 'nullable|numeric|min:0',
        'distributor_id' => 'nullable|integer|exists:distributors,id',
        'tso_id' => 'nullable|array',
        'tso_id.*' => 'integer|exists:users,id',
        'route_id' => 'nullable|integer|exists:routes,id',
        'sub_route_id' => 'nullable|integer|exists:sub_routes,id',
    ], [
        'shop_code.unique' => 'This shop code is already taken.',
        'email.email' => 'Please enter a valid email address.',
        'alt_email.email' => 'Please enter a valid alternate email address.',
        'image.image' => 'The file must be an image.',
        'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
        'image.max' => 'The image may not be greater than 2MB.',
        'latitude.between' => 'Latitude must be between -90 and 90.',
        'longitude.between' => 'Longitude must be between -180 and 180.',
        'tso_id.*.exists' => 'One or more selected TSOs do not exist.',
        'distributor_id.exists' => 'Selected distributor does not exist.',
        'shop_type_id.exists' => 'Selected shop category does not exist.',
        'channel_id.exists' => 'Selected shop channel does not exist.',
        'shop_zone_id.exists' => 'Selected zone does not exist.',
        'route_id.exists' => 'Selected route does not exist.',
        'sub_route_id.exists' => 'Selected sub route does not exist.',
        'city.exists' => 'Selected city does not exist.',
        'allow_credit_days.min' => 'Credit days cannot be negative.',
        'allow_credit_amount.min' => 'Credit amount cannot be negative.',
        'invoice_discount.max' => 'Invoice discount cannot exceed 100%.',
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422);
    }

    DB::beginTransaction();
    try {
        // Only take fields that are present in the request
        $data = $request->except('tso_id', 'shop_location', 'image');
        
        // Shop location check - only if shop_location field is present
        if ($request->has('shop_location')) {
            if ($request->shop_location != 1) {
                $data['latitude'] = null;
                $data['longitude'] = null;
                $data['location_radius'] = null;
            }
        }

        // Phone number correction - only if mobile_no is present
        if ($request->has('mobile_no') && !empty($request->mobile_no)) {
            $data['mobile_no'] = MasterFormsHelper::correctPhoneNumber($request->mobile_no);
        }

        // Image handling - only if image file is present
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '-' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());

            // Delete previous image
            if ($shop->image && Storage::disk('public')->exists('shop_image/' . $shop->image)) {
                Storage::disk('public')->delete('shop_image/' . $shop->image);
            }

            // Store new image
            $file->storeAs('shop_image', $fileName, 'public');
            $data['image'] = $fileName;
        }

        // Update the shop - only with fields that are present
        if (!empty($data)) {
            $shop->update($data);
        }

        // Update TSO relationships - only if tso_id is present
        if ($request->has('tso_id')) {
            if ($request->filled('tso_id')) {
                $tsoIds = is_array($request->tso_id) 
                        ? array_map('intval', $request->tso_id)
                        : [(int)$request->tso_id];
                
                $shop->tsos()->sync($tsoIds);
            } else {
                // If tso_id is present but empty, detach all
                $shop->tsos()->detach();
            }
        }

        DB::commit();
        
        // Load only relationships that definitely exist in your Shop model
        // Remove any relationships that might not exist
        // $shop->load(['tsos', 'distributor', 'route', 'shopType', 'zone']);
        
        // Or if you're not sure which relationships exist, just return the shop without loading
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Shop updated successfully.',
        //     'data' => $shop
        // ], 200);
        
        return response()->json([
            'success' => true,
            'message' => 'Shop updated successfully.',
            'data' => $shop
        ], 200);
        
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Failed to update shop.',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function ShopAttendenceBulk(Request $request)
{
    $attendances = $request->json()->all();

    $response = [
        'success' => [],
        'failed'  => []
    ];

    foreach ($attendances as $attendance) {
        try {
            // Force local_id as string
            $attendance['local_id'] = (string) ($attendance['local_id'] ?? '');

            /* ================= DUPLICATE LOCAL ID CHECK ================= */
            if (!empty($attendance['local_id'])) {
                $existing = ShopAttendence::where('local_id', $attendance['local_id'])->first();
                if ($existing) {
                    $response['success'][] = [
                        'local_id'      => $attendance['local_id'],
                        'attendance_id' => $existing->id,
                        'message'       => 'Attendance with this local_id already exists'
                    ];
                    continue; // skip insert
                }
            }

            /* ================= VALIDATION ================= */
            $validator = Validator::make($attendance, [
                'local_id'       => 'required',
                'distributor_id' => 'required|integer|exists:distributors,id',
                'tso_id'         => 'required|integer|exists:tso,id',
                'shop_id'        => 'required|integer|exists:shops,id',
                'check_in'       => 'required|date_format:Y-m-d H:i:s',
                'check_out'      => 'required|date_format:Y-m-d H:i:s|after_or_equal:check_in',
            ]);

            if ($validator->fails()) {
                $response['failed'][] = [
                    'local_id' => $attendance['local_id'] ?? null,
                    'errors'   => $validator->errors()->all()
                ];
                continue;
            }

            /* ================= CREATE ATTENDANCE ================= */
            $att = ShopAttendence::create([
                'distributor_id' => $attendance['distributor_id'],
                'tso_id'         => $attendance['tso_id'],
                'shop_id'        => $attendance['shop_id'],
                'check_in'       => $attendance['check_in'],
                'check_out'      => $attendance['check_out'],
                'sync_date_time' => now(),
                'local_id'       => $attendance['local_id'],
            ]);

            $response['success'][] = [
                'local_id'      => $attendance['local_id'],
                'attendance_id' => $att->id,
                'message'       => 'Attendance inserted successfully'
            ];

        } catch (\Exception $e) {
            $response['failed'][] = [
                'local_id' => $attendance['local_id'] ?? null,
                'error'    => $e->getMessage()
            ];
        }
    }

    return response()->json($response, 201);
}

    public function updateCordinates(Request $request , $id)
    {

        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        $shop = Shop::find($request->id)->update($request->only('latitude', 'longitude'));
        $shop = new Shop();
        $shop  = $shop ->find($id);
        return response()->json(['data'=>$shop,'success' => 'Cordinates Updated successfully.']);
    }
}
