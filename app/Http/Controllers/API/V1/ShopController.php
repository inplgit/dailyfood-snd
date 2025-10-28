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



public function userWiseShopList(Request $request)
{
    $request->validate([
        'route_id' => 'required|exists:routes,id'
    ]);

    $tsoId = Auth::user()->tso->id;
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
