<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\V1\BaseController;
use App\Models\User;
use App\Models\Distributor;
use App\Helpers\MasterFormsHelper;
use App\Models\shop;
use App\Models\Route;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
class RouteController extends BaseController
{
  
    public $master;

    public function __construct()
    {
        $this->master = new MasterFormsHelper();
    }
public function GetTsoByDistributor(Request $request)
{
    $distributor = $request->distributor_id;
    $tso = $this->master->get_all_tso_by_distributor_id($distributor);
    return $this->sendResponse($tso,'TSO Retrieved Successfully');
}
 public function getTsoDistributorWiseRoute_old(Request $request ,$distributor_id)
    {
        // dd(date('l'));
        $tso_id = User::find(Auth::user()->id)->tso->id;

       $routes=   Route::status()->where('tso_id',$tso_id)->where('distributor_id',$distributor_id)
    //    ->with('routeday')
       ->whereHas('RouteDay', function ($query) {
         $query->where('day',date('l'));
        })
        // ->with(['RouteDay' => function ($query) {
        //     $query->where('day', date('l')); // Load only the current day
        // }])
        ->withCount('shops')
        ->get()
        ->map(function($route) {
            $route->day = $route->RouteDay->pluck('day')->implode(', ');
            unset($route->RouteDay);
            return $route;
            // if ($route->routeday->isNotEmpty()) {
            //     $route->day = $route->routeday->first()->day; // Get the first true record
            // } else {
            //     $route->day = null; // Or you can set a default value
            // }
            // return $route;
        })
        ;


        return $this->sendResponse($routes,'Route Retrive Successfully');
    }




public function getTsoDistributorWiseRoute(Request $request, $distributor_id)
{
    $tso_id = Auth::user()->tso->id;

    

    $routes = DB::table('route_tso as rt')
        ->join('routes as r', 'rt.route_id', '=', 'r.id')
        ->leftJoin('users as u', 'u.id', '=', 'rt.tso_id')
        ->leftJoin('shops as s', 's.route_id', '=', 'r.id')
        ->leftJoin('route_days as rd', 'rd.route_id', '=', 'r.id')
      ->select(
    'r.id',
    'r.route_name',
    'r.distributor_id',
    'rt.tso_id',  // ➜ now appears in the JSON
    'r.status',
    'u.username',
    'r.created_at',
    'r.updated_at',
    DB::raw('COUNT(DISTINCT s.id) AS shops_count'),
    DB::raw("GROUP_CONCAT(DISTINCT rd.day ORDER BY rd.day SEPARATOR ', ') AS day")
)

        ->where('rt.tso_id', $tso_id)
        ->where('r.distributor_id', $distributor_id)
        ->where('r.status', 1)
        ->groupBy(
            'r.id', 'r.route_name', 'r.distributor_id',
            'rt.tso_id', 'r.status',
            'u.username', 'r.created_at', 'r.updated_at'
        )
        ->get();

    return $this->sendResponse($routes, 'Routes Retrieved Successfully');
}




public function getTsoDistributorWiseRoute_shop_old(Request $request, $distributor_id)
{
    $tso_id = Auth::user()->tso->id;
    $today  = Carbon::now()->englishDayOfWeek;

    $routes = DB::table('routes as r')
        ->join('route_tso as rt', 'rt.route_id', '=', 'r.id')
        ->leftJoin('users as u', 'u.id', '=', 'rt.tso_id')
        ->leftJoin('shops as s', 's.route_id', '=', 'r.id')
        ->leftJoin('route_days as rd', 'rd.route_id', '=', 'r.id')
      ->select(
    'r.id',
    'r.route_name',
    'r.distributor_id',
    'rt.tso_id',
    'r.status',
    'u.username',
    'r.created_at',
    'r.updated_at',
    DB::raw('GROUP_CONCAT(DISTINCT CONCAT(s.company_name, " (", s.address, ")") SEPARATOR ", ") as shop_info'),
    	DB::raw('GROUP_CONCAT(DISTINCT rd.day SEPARATOR ", ") as days')
	)

        ->where('rt.tso_id', $tso_id)
        ->where('r.distributor_id', $distributor_id)
        ->where('r.status', 1)
        ->whereExists(function ($q) use ($today) {
            $q->select(DB::raw(1))
              ->from('route_days as rd2')
              ->whereColumn('rd2.route_id', 'r.id')
              ->where('rd2.day', $today);
        })
        ->groupBy('r.id', 'r.route_name', 'r.distributor_id', 'rt.tso_id', 'r.status', 'u.username', 'r.created_at', 'r.updated_at')
        ->get();


        $data = [];

foreach ($routes as $route) {
    $shop_info_list = explode(',', $route->shop_info); // company_name (address)
    $days = implode(', ', array_unique(explode(',', $route->days)));

    foreach ($shop_info_list as $shop_info) {
        // Break shop name and address
        if (preg_match('/^(.*?) \((.*?)\)$/', trim($shop_info), $matches)) {
            $shop_name = $matches[1];
            $address = $matches[2];
        } else {
            $shop_name = trim($shop_info);
            $address = null;
        }

        $data[] = [
            'route_id'       => $route->id,
            'route_name'     => $route->route_name,
            'distributor_id' => $route->distributor_id,
            'tso_id'         => $route->tso_id,
            'status'         => $route->status,
            'created_at'     => $route->created_at,
            'updated_at'     => $route->updated_at,
            'shop_name'      => $shop_name,
            'address'        => $address,
            'days'           => $days,
        ];
    }
}


return $this->sendResponse($data, 'Route Retrieved Successfully');


}


public function getTsoDistributorWiseRoute_shop(Request $request, $distributor_id)
{
    $tso_id = Auth::user()->tso->id;
$route_id = $request->query('route_id');

    // Visited shop IDs
    $visited_shop_ids = DB::table('shop_visits')
        ->where('user_id', Auth::id())
        ->whereDate('visit_date', Carbon::today())
        ->pluck('shop_id')
        ->map(fn($id) => (int) $id)
        ->toArray();

    // Ordered shop IDs
    $ordered_shop_ids = Auth::user()->salesOrder()
        ->status()
        ->whereDate('created_at', Carbon::today())
        ->pluck('shop_id')
        ->map(fn($id) => (int) $id)
        ->toArray();

    // Routes assigned to TSO
    $routes = DB::table('route_tso as rt')
        ->join('routes as r', 'rt.route_id', '=', 'r.id')
        ->leftJoin('users as u', 'u.id', '=', 'rt.tso_id')
        ->leftJoin('route_days as rd', 'rd.route_id', '=', 'r.id')
        ->select(
            'r.id as route_id',
            'r.route_name',
            'r.distributor_id',
            'rt.tso_id',
            'r.status',
            'u.username',
            'r.created_at',
            'r.updated_at',
            DB::raw('GROUP_CONCAT(DISTINCT rd.day SEPARATOR ", ") as days')
        )
        ->where('rt.tso_id', $tso_id)
        ->where('r.distributor_id', $distributor_id)
    ->when($route_id, fn($query) => $query->where('r.id', $route_id)) // <-- this line added
        ->where('r.status', 1)
        ->groupBy(
            'r.id', 'r.route_name', 'r.distributor_id',
            'rt.tso_id', 'r.status', 'u.username',
            'r.created_at', 'r.updated_at'
        )
        ->get();

    $final_data = [];

    foreach ($routes as $route) {
        // Shops assigned to this TSO and route
        $shop_ids = DB::table('shop_tso')
            ->where('tso_id', $route->tso_id)
            ->pluck('shop_id')
            ->toArray();

        // $shops = DB::table('shops')
        //     ->whereIn('id', $shop_ids)
        //     ->where('route_id', $route->route_id)
        //     ->get();


        $shops = DB::table('shops')
    ->whereIn('id', $shop_ids)
    ->where('route_id', $route->route_id)
    ->where('status', 1)
    ->where('active', 1)
    ->get();
        $shop_list = [];

        foreach ($shops as $shop) {
            $shop_status = 'normal';
            $visit_details = null;
            $orders = [];

            // Visit check
            $visit = DB::table('shop_visits')
                ->where('shop_id', $shop->id)
                ->where('user_id', Auth::id())
                ->whereDate('visit_date', Carbon::today())
                ->first();

            if (in_array($shop->id, $ordered_shop_ids)) {
                $shop_status = 'productive';
            } elseif ($visit) {
                $shop_status = 'visited';
            }

            // Only if visited
            if ($shop_status === 'visited' && $visit) {
                $visit_details = [
                    'id'         => $visit->id,
                    'visit_date' => $visit->visit_date,
                    'location'   => $visit->location ?? null,
                    'note'       => $visit->note ?? null,
                    'type'       => $visit->type ?? null,
                ];
            }

            // Only if productive
            if ($shop_status === 'productive') {
                $orders = DB::table('sale_orders')
                    ->select('id as order_id', 'created_at as order_date', 'total_amount as total_amount', 'status')
                    ->where('shop_id', $shop->id)
                    ->whereDate('created_at', Carbon::today())
                    ->get();
            }

            // Build shop data based on status
            $shop_data = [
                'shop_id'     => $shop->id,
                'shop_name'   => $shop->company_name,
                'address'     => $shop->address,
                'latitude'    => $shop->latitude,
                'longitude'   => $shop->longitude,
                'shop_status' => $shop_status,
            ];

            if ($shop_status === 'visited') {
                $shop_data['visit_details'] = $visit_details;
            }

            if ($shop_status === 'productive') {
                $shop_data['orders'] = $orders;
            }

            $shop_list[] = $shop_data;
        }

        $final_data[] = [
            'route_id'       => $route->route_id,
            'route_name'     => $route->route_name,
            'distributor_id' => $route->distributor_id,
            'tso_id'         => $route->tso_id,
            'status'         => $route->status,
            'created_at'     => $route->created_at,
            'updated_at'     => $route->updated_at,
            'days'           => $route->days,
            'shops'          => $shop_list,
        ];
    }

    return response()->json([
        'success' => true,
        'data'    => $final_data,
        'message' => 'Route and shop data retrieved successfully'
    ]);
}




// public function getTsoDistributorWiseRoute(Request $request, $distributor_id)
// {
//     $tso_id = User::find(Auth::user()->id)->tso->id;

//     $routes = Route::status()
//         ->where('tso_id', $tso_id)
//         ->where('distributor_id', $distributor_id)
//         ->with('RouteDay') // ab poora relation load hoga bina kisi filter ke
//         ->withCount('shops')
//         ->get()
//         ->map(function ($route) {
//             $route->day = $route->RouteDay->pluck('day')->implode(', ');
//             unset($route->RouteDay);
//             return $route;
//         });

//     return $this->sendResponse($routes, 'Route Retrieved Successfully');
// }


//     public function getRoutePlan($distributor_id)
// {
//     $tso_id = Auth::user()->tso->id;   // لاگ اِن TSO

//     $routes = DB::table('routes as r')
//         // ───── joins ─────────────────────────────────────────────
//         ->join('route_tso   as rt', 'rt.route_id', '=', 'r.id')  // ↳ tso_id
//         ->leftJoin('users   as u',  'u.id',       '=', 'rt.tso_id')
//         ->leftJoin('shops   as s',  's.route_id', '=', 'r.id')   // ↳ shops_count
//         ->leftJoin('route_days as rd', 'rd.route_id', '=', 'r.id') // ↳ تمام دن

//         // ───── columns ──────────────────────────────────────────
//         ->select(
//             'r.id',
//             'r.route_name',
//             'r.distributor_id',
//             'rt.tso_id',
//             'r.status',
//             'u.username',
//             'r.created_at',
//             'r.updated_at',
//             DB::raw('COUNT(DISTINCT s.id) AS shops_count'),
//             DB::raw("GROUP_CONCAT(DISTINCT rd.day ORDER BY rd.day SEPARATOR ', ') AS day")
//         )

//         // ───── filters ──────────────────────────────────────────
//         ->where('rt.tso_id',        $tso_id)
//         ->where('r.distributor_id', $distributor_id)
//         ->where('r.status',         1)      // status() scope equivalent

//         // ───── group‑by (ہر non‑aggregated column) ─────────────
//         ->groupBy(
//             'r.id', 'r.route_name', 'r.distributor_id',
//             'rt.tso_id', 'r.status',
//             'u.username', 'r.created_at', 'r.updated_at'
//         )
//         ->get();

//     return $this->sendResponse($routes, 'Route Retrieved Successfully');
// }

public function getRoutePlan($distributor_id)
{
    $tso_id = Auth::user()->tso->id;

    $routes = DB::table('routes as r')
        ->join('route_tso   as rt', 'rt.route_id', '=', 'r.id')
        ->leftJoin('users   as u',  'u.id',       '=', 'rt.tso_id')
        ->leftJoin('shops   as s',  function ($join) {
            $join->on('s.route_id', '=', 'r.id')
                 ->where('s.status', 1)   // ✅ only active shops
                 ->where('s.active', 1);  // ✅ only active flag
        })
        ->leftJoin('route_days as rd', 'rd.route_id', '=', 'r.id')
        ->select(
            'r.id',
            'r.route_name',
            'r.distributor_id',
            'rt.tso_id',
            'r.status',
            'u.username',
            'r.created_at',
            'r.updated_at',
            DB::raw('COUNT(DISTINCT s.id) AS shops_count'),
            DB::raw("GROUP_CONCAT(DISTINCT rd.day ORDER BY rd.day SEPARATOR ', ') AS day")
        )
        ->where('rt.tso_id', $tso_id)
        ->where('r.distributor_id', $distributor_id)
        ->where('r.status', 1)
        ->groupBy(
            'r.id', 'r.route_name', 'r.distributor_id',
            'rt.tso_id', 'r.status',
            'u.username', 'r.created_at', 'r.updated_at'
        )
        ->get();

    return $this->sendResponse($routes, 'Route Retrieved Successfully');
}


}
