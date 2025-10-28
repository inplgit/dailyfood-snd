<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRouteRequest;
use App\Http\Requests\UpdateRouteRequest;
use App\Helpers\MasterFormsHelper;
use Illuminate\Http\Request;
use App\Models\Route;
use App\Models\RouteDay;
use App\Models\Distributor;
use App\Models\ActivityLog;
use App\Models\SubRoutes;
use App\Models\RouteTso;
use App\Models\Shop;
use App\Models\TSO;
use DB;
use Illuminate\Support\Facades\Log;

class RouteController extends Controller
{
    public $master;
    public $page;
    public function __construct()
    {
        $this->page = 'pages.Routes.Route.';
        $this->master = new MasterFormsHelper();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,Route $route)
    {

        $route =  $route::status()->get();
       if ($request->ajax()):
           return  view($this->page.'RouteListAjax',compact('route'));
        endif;
       return  view($this->page.'RouteList');
    }

public function AddRouteMultiTso_index(Request $request ,Route $route)
{
    $route =  $route::status()->get();
 if ($request->ajax()):
        return view($this->page.'RouteListAjax_MultiTso', compact('route'));
    endif;

    return view($this->page.'RouteList_MultiTso');
}


 public function route_create_tso_multi()
    {
        return  view($this->page.'AddRouteMultiTso');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return  view($this->page.'AddRoute');
    }

public function AddRouteMultiTso_Store(Request $request)
{

    $request->validate([
        'routes' => 'required|array',
        'routes.*.route_name' => 'required|string|max:255',
        'routes.*.distributor_id' => 'required|integer|exists:distributors,id',
        'routes.*.tso_id' => 'nullable|array', // changed from integer to array
        'routes.*.tso_id.*' => 'nullable|integer|exists:tso,id', // note: tsos not tso
        'routes.*.day' => 'required|array|min:1',
        'routes.*.day.*' => 'required|string',
    ]);
    

    DB::beginTransaction();

    try {
        foreach ($request->routes as $routeData) {
            // Create route
            $route = Route::create([
                'route_name' => $routeData['route_name'],
                'distributor_id' => $routeData['distributor_id'],
            ]);

            // Save days
            foreach ($routeData['day'] as $day) {
                RouteDay::create([
                    'route_id' => $route->id,
                    'day' => $day,
                ]);
            }

            // Save TSO mapping if present
            if (!empty($routeData['tso_id']) && is_array($routeData['tso_id'])) {
                foreach ($routeData['tso_id'] as $tsoId) {
                    RouteTso::create([
                        'route_id' => $route->id,
                        'tso_id' => $tsoId,
                    ]);
                }
            }
        }

        DB::commit();
        return response()->json(['success' => 'Routes created successfully.']);
    } catch (\Throwable $th) {
        DB::rollBack();
        Log::error('Route Creation Error: ' . $th->getMessage());
        return response()->json(['error' => 'Failed to create routes. Please try again.']);
    }
}




public function AddRouteMultiTso_edit(Route $route)
{
   
    $route->loadMissing(['distributor', 'routeDays', 'routeTsos']);

    // Default to empty array if null
    $route_day = $route->routeDays ? $route->routeDays->pluck('day')->toArray() : [];
    $route_tsos = $route->routeTsos ? $route->routeTsos->pluck('tso_id')->toArray() : [];

    $distributor_tso = Tso::where('distributor_id', $route->distributor_id)->get();

  

    return view('pages.Routes.Route.AddRouteMultiTso_edit', compact(
        'route',
        'route_day',
        'route_tsos',
        'distributor_tso'
    ));
}


public function AddRouteMultiTso_update(Request $request, Route $route)
{
    $request->validate([
        'route_name'      => 'required|string|max:255',
        'distributor_id'  => 'required|integer|exists:distributors,id',
        'tso_id'          => 'nullable|array',
        'tso_id.*'        => 'nullable|integer|exists:tso,id',
        'day'             => 'required|array|min:1',
        'day.*'           => 'required|string',
    ]);

    DB::beginTransaction();
    try {
        // 1. update main record
        $route->update([
            'route_name'     => $request->route_name,
            'distributor_id' => $request->distributor_id,
        ]);

        // 2. refresh days
        $route->routeDays()->delete();
        foreach ($request->day as $d) {
            $route->routeDays()->create(['day' => $d]);
        }

        // 3. refresh TSOs
        $route->routeTsos()->delete();
        foreach ($request->tso_id ?? [] as $tsoId) {
            $route->routeTsos()->create(['tso_id' => $tsoId]);
        }

        DB::commit();
        return back()->with('success', 'Route updated successfully.');
    } catch (\Throwable $e) {
        DB::rollBack();
        \Log::error('Route Update Error: ' . $e->getMessage());
        return back()->with('error', 'Failed to update route, please try again.');
    }
}



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(StoreRouteRequest $request)
    // {

    //     DB::beginTransaction();
    //     try {
    //         $requestData = $request->except(['day']);
    //         $data =   Route::create($requestData);

    //         foreach ($request->day as $day):
    //             RouteDay::create(['route_id'=>$data->id,'day'=>$day]);
    //         endforeach;

    //          // dd($route->RouteDay->pluck('day'));
    //         $route_log = $data->toArray();
    //         $log_data = array_merge(
    //             $route_log, // Merge the original array
    //             [
    //                 'route_id' => $data->id, // Add additional data
    //                 'route_days' => $data->RouteDay->pluck('day')->toArray(), // Pluck IDs and convert to array
    //             ]
    //         );
    //         MasterFormsHelper::activity_log_submit($data,$log_data,'route',2, 'Route Create');



    //         DB::commit();

    //         return response()->json(['success' => 'Route created successfully.']);
    //     } catch (\Throwable $th) {
    //         DB::rollBack();

    //         return response()->json(['catchError' => $th->getMessage()]);
    //     }
    // }

    // public function store(Request $request)
    // {
    //     DB::beginTransaction();

    //     dd($request->all());
    //     try {
    //         foreach ($request->routes as $routeData) {
    //             $route = Route::create([
    //                 'route_name' => $routeData['route_name'],
    //                 'distributor_id' => $routeData['distributor_id'],
    //                 'tso_id' => $routeData['tso_id'],
    //             ]);
                
    //             foreach ($routeData['day'] as $day) {
    //                 RouteDay::create([
    //                     'route_id' => $route->id,
    //                     'day' => $day,
    //                 ]);
    //             }
    //         }
    //         DB::commit();
    //         return response()->json(['success' => 'Routes created successfully.']);
    //     } catch (\Throwable $th) {
    //         DB::rollBack();
    //         return response()->json(['error' => $th->getMessage()]);
    //     }
    // }

    public function store(Request $request)
{
    $request->validate([
        'routes.*.route_name' => 'required|string|max:255',
        'routes.*.distributor_id' => 'required|integer|exists:distributors,id',
        'routes.*.tso_id' => 'nullable|integer|exists:tso,id',
        'routes.*.day' => 'required|array',
        'routes.*.day.*' => 'required|string',
    ]);

    DB::beginTransaction();
    try {
        foreach ($request->routes as $routeData) {
            $route = Route::create([
                'route_name' => $routeData['route_name'],
                'distributor_id' => $routeData['distributor_id'],
                'tso_id' => $routeData['tso_id'] ?? null,
            ]);

            foreach ($routeData['day'] as $day) {
                RouteDay::create([
                    'route_id' => $route->id,
                    'day' => $day,
                ]);
            }
        }

        DB::commit();
        return response()->json(['success' => 'Routes created successfully.']);
    } catch (\Throwable $th) {
        DB::rollBack();
        Log::error('Route Creation Error: ' . $th->getMessage());
        return response()->json(['error' => 'Failed to create routes. Please try again.']);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Route $route)
    {
        $route_day=RouteDay::where('route_id',$route->id)->pluck('day')->toArray();
        return  view($this->page.'EditRoute',compact('route','route_day'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRouteRequest $request, Route $route)
    {
        DB::beginTransaction();
        try {
            $route_id = $route->id;
            $requestData = $request->except(['day']);
            $requestData['day'] = $request->day[0];
            $route->update($requestData);


            Shop::where('route_id',$route_id)->update(['tso_id'=>$request->tso_id , 'distributor_id'=>$request->distributor_id]);


            RouteDay::where('route_id',$route_id)->delete();
            foreach ($request->day as $day):
                RouteDay::create(['route_id'=>$route_id,'day'=>$day]);
            endforeach;

            // dd($route->RouteDay->pluck('day'));
            $route_log = $route->toArray();
                $log_data = array_merge(
                    $route_log, // Merge the original array
                    [
                        'route_id' => $route->id, // Add additional data
                        'route_days' => $route->RouteDay->pluck('day')->toArray(), // Pluck IDs and convert to array
                    ]
                );
            MasterFormsHelper::activity_log_submit($route,$log_data,'route',2 , 'Route Update');

            DB::commit();

            return response()->json(['success' => 'Updated successfully.']);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['catchError' => $th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Route::where('id',$id)->update(['status'=>0]);
        return response()->json(['success'=>'Deleted Successfully!']);
    }

    public function route_log(Request $request)
    {

        if ($request->ajax()):
            $routeId = $request->route_id;
            // dd($routeId);
            $log_data = ActivityLog::where('table_type', Route::class)
            ->when($routeId != null , function ($query) use ($routeId){
                return $query->where('table_id', $routeId);
            })
            ->get();
            // dd($log_data->toArray());
           return  view($this->page.'RouteLogAjax',compact('log_data'));
        endif;
       return  view($this->page.'RouteLog');
    }


    public function TSODayWisePlanner(Request $request)
    {
       $distributor_id= $request->distribuotr_id;
       $tso_id= $request->tso_id;
       $tso = Route::status()->where('distributor_id',$distributor_id)->where('tso_id',$tso_id)->get();

       if ($request->ajax()):
        return view($this->page.'TSODayWisePlannerAjax',compact('tso'));
       endif;

        return view($this->page.'TSODayWisePlanner');
    }

    public function route_tso_wise(Request $request ,Route $route)
    {
      $day = $request->day;
      $ids = $request->ids;

      foreach($day as $key => $row):
        RouteDay::where('route_id',$ids[$key])->delete();
        foreach ($request->day[$key] as $day):
            RouteDay::create(['route_id'=>$ids[$key],'day'=>$day]);
        endforeach;
    //   $route->where('id',$ids[$key])->update(['day'=>$day[$key]]);
      endforeach;

      return redirect()->back()->with('success', 'Routes Updated');
    }


    public function route_transfer(Request $request)
    {
        $distributor_id = $request->distribuotr_id;
        $tso_id = $request->tso_id;

        $routeIds = DB::table('route_tso')
            ->where('tso_id', $tso_id)
            ->pluck('route_id');

        $tso = Route::status()
            ->where('distributor_id', $distributor_id)
            ->whereIn('id', $routeIds)
            ->get();

        if ($request->ajax()) return view($this->page . 'RouteTransferAjax', compact('tso', 'tso_id'));

        return view($this->page . 'RouteTransfer');
        //   $distributor_id= $request->distribuotr_id;
        //   $tso_id= $request->tso_id;
        //   $tso = Route::status()->where('distributor_id',$distributor_id)->where('tso_id',$tso_id)->get();
        //   if ($request->ajax()):
        //    return view($this->page.'RouteTransferAjax',compact('tso' , 'tso_id'));
        //   endif;
        //    return view($this->page.'RouteTransfer');
    }

    // public function route_transfer_store(Request $request,Route $route)
    // {
    //     // dd($request->all());
    //     $tso_ids = $request->tso_ids;
    //     $distributor_ids = $request->distributor_ids;
    //     $ids = $request->ids;
    //     foreach ($ids as $key => $routeId) {
    //         if (isset($tso_ids[$key]) && !empty($tso_ids[$key])) {
    //             $tsoId = $tso_ids[$key];
    //             $distributorId = $distributor_ids[$key];
    //             Shop::where('route_id', $routeId)
    //                 ->update([
    //                     'tso_id' => $tsoId,
    //                     'distributor_id' => $distributorId,
    //                 ]);
    //             DB::table('route_tso')
    //                 ->where('route_id', $routeId)
    //                 ->delete();
    //             DB::table('route_tso')->insert([
    //                 'route_id' => $routeId,
    //                 'tso_id'   => $tsoId,
    //             ]);
    //             $shopIds = Shop::where('route_id', $routeId)->pluck('id');
    //             if ($shopIds->isNotEmpty()) {
    //                 DB::table('shop_tso')
    //                     ->whereIn('shop_id', $shopIds)
    //                     ->delete();
    //                 $insertData = $shopIds->map(function ($shopId) use ($tsoId) {
    //                     return [
    //                         'shop_id' => $shopId,
    //                         'tso_id'  => $tsoId,
    //                     ];
    //                 })->toArray();

    //                 DB::table('shop_tso')->insert($insertData);
    //             }
    //         }
    //     }

    //     return redirect()->back()->with('success', 'Routes transferred successfully');

    // //   $tso_ids = $request->tso_ids;
    // //   $distributor_ids = $request->distributor_ids;
    // //   $ids = $request->ids;
    // // // dd($tso_ids , $distributor_ids , $ids);
    // //   foreach($ids as $key => $row):
    // //     if (isset($tso_ids[$key])) {
    // //         $tso = TSO::find($tso_ids[$key]);
    // //         // dd($tso->toArray());
    // //         Shop::where('route_id',$ids[$key])->update(['tso_id'=>$tso_ids[$key] , 'distributor_id'=>$distributor_ids[$key]]);
    // //         Route::where('id',$ids[$key])->update(['tso_id'=>$tso_ids[$key] , 'distributor_id'=>$distributor_ids[$key]]);
    // //     }
    // //     // dd(Shop::where('route_id',$ids[$key])->get() , Route::where('id',$ids[$key])->get() ,$tso_ids[$key]) ;
    // //   endforeach;

    // //   return redirect()->back()->with('success', 'Routes Tranfer Successfully');
    // }
    public function route_transfer_store_old(Request $request, Route $route)
    {
        // dd($request->all());
        $tso_ids = $request->tso_ids;
        $distributor_ids = $request->distributor_ids;
        $ids = $request->ids;

        foreach ($ids as $key => $routeId) {
            if (isset($tso_ids[$key]) && !empty($tso_ids[$key])) {
                $tsoId = $tso_ids[$key];
                $distributorId = $distributor_ids[$key];

                // 🔹 Update in Shop table
                Shop::where('route_id', $routeId)
                    ->update([
                        'tso_id' => $tsoId,
                        'distributor_id' => $distributorId,
                    ]);

                // 🔹 Update in Route table
                Route::where('id', $routeId)
                    ->update([
                        'distributor_id' => $distributorId,
                    ]);

                // 🔹 Update in route_tso table
                DB::table('route_tso')
                    ->where('route_id', $routeId)
                    ->delete();

                DB::table('route_tso')->insert([
                    'route_id' => $routeId,
                    'tso_id'   => $tsoId,
                ]);

                // 🔹 Update in shop_tso table
                $shopIds = Shop::where('route_id', $routeId)->pluck('id');
                if ($shopIds->isNotEmpty()) {
                    DB::table('shop_tso')
                        ->whereIn('shop_id', $shopIds)
                        ->delete();

                    $insertData = $shopIds->map(function ($shopId) use ($tsoId) {
                        return [
                            'shop_id' => $shopId,
                            'tso_id'  => $tsoId,
                        ];
                    })->toArray();

                    DB::table('shop_tso')->insert($insertData);
                }
            }
        }

        return redirect()->back()->with('success', 'Routes transferred successfully');
    }
    public function route_transfer_store(Request $request)
    {
        $tso_ids         = $request->tso_ids;
        $distributor_ids = $request->distributor_ids;
        $ids             = $request->ids;

        foreach ($ids as $key => $routeId) {

            // Get selected TSO array for this route
            $selectedTsoIds = $tso_ids[$key] ?? [];

            if (!empty($selectedTsoIds)) {

                $distributorId = $distributor_ids[$key];

                // 🔸 1. Update Route table (distributor only)
                Route::where('id', $routeId)->update([
                    'distributor_id' => $distributorId,
                ]);

                // 🔸 2. Update Shop table (if needed)
                // Choose the first TSO ID as the "main" tso_id for the shop table
                $primaryTsoId = $selectedTsoIds[0];

                Shop::where('route_id', $routeId)->update([
                    'tso_id'         => $primaryTsoId,
                    'distributor_id' => $distributorId,
                ]);

                // 🔸 3. Update route_tso table (many-to-many)
                DB::table('route_tso')->where('route_id', $routeId)->delete();

                $routeTsoInsert = collect($selectedTsoIds)->map(function ($tsoId) use ($routeId) {
                    return [
                        'route_id' => $routeId,
                        'tso_id'   => $tsoId,
                    ];
                })->toArray();

                DB::table('route_tso')->insert($routeTsoInsert);

                // 🔸 4. Update shop_tso table (many-to-many)
                $shopIds = Shop::where('route_id', $routeId)->pluck('id');

                if ($shopIds->isNotEmpty()) {
                    DB::table('shop_tso')->whereIn('shop_id', $shopIds)->delete();

                    $shopTsoInsert = [];
                    foreach ($shopIds as $shopId) {
                        foreach ($selectedTsoIds as $tsoId) {
                            $shopTsoInsert[] = [
                                'shop_id' => $shopId,
                                'tso_id'  => $tsoId,
                            ];
                        }
                    }

                    DB::table('shop_tso')->insert($shopTsoInsert);
                }
            }
        }

        return redirect()->back()->with('success', 'Routes transferred successfully with multiple TSO!');
    }

    public function GetTsoByDistributor(Request $request)
    {
        $distributor = $request->distribuotr_id;
        $tso = $this->master->get_all_tso_by_distributor_id($distributor);
        return  response()->json(['tso'=>$tso]);
    }
    public function GetTsoByDistributormulti(Request $request)
    {
        $distributor = $request->distributor_id; // Corrected typo
        if (!$distributor) {
            return response()->json(['tso' => []], 400); // Return error if distributor_id is missing
        }
    
        $tso = $this->master->get_all_tso_by_distributor_id_multi($distributor);
    
        return response()->json(['tso' => $tso]);
    }
    // Alii :)
    public function GetTsoByDistributorMultiNew(Request $request)
    {
        $distributor_ids = $request->input('distributor_id'); 
        // This will be an array if multiple were passed
        if (!$distributor_ids) {
            return response()->json(['tso' => []], 400); // Return error if distributor_id is missing
        }
        $tsos = TSO::status()->whereHas('UserDistributor', function ($query) use ($distributor_ids) {
            $query->whereIn('distributor_id', $distributor_ids)
                ->groupBy('user_id');
        })->get();
        return response()->json(['tso' => $tsos]);
    }
    // Alii :)
    

    public function GetAllTsoByDistributor(Request $request)
    {
        $distributor = $request->distribuotr_id;
        $tso = $this->master->get_all_tso_by_distributor_id($distributor , false);
        return  response()->json(['tso'=>$tso]);
    }

    public function GetTsoByMultipleDistributor(Request $request)
    {
        // dd($request->distribuotr_id);
        $distributor = $request->distribuotr_id;
        $tso = $this->master->get_all_tso_by_distributor_ids($distributor);
        return  response()->json(['tso'=>$tso]);
    }
     // public function GetRouteBYTSO(Request $request)
    // {
    //     $tso_id = $request->tso_id;
    //     $route = $this->master->get_all_route_by_tso($tso_id);
    //     return  response()->json(['route'=>$route]);
    // }

       public function GetRouteBYTSO(Request $request)
    {
        $tso_id = $request->tso_id;
        $route = $this->master->get_all_route_by_tso_multi($tso_id);
        return  response()->json(['route'=>$route]);
    }
    public function get_sub_route(Request $request)
    {
        $route_id = $request->route_id;
        $route = SubRoutes::status()->where('route_id',$route_id)->get();
        return  response()->json(['route'=>$route]);
    }


    function get_distributor_by_city(Request $request){
        // dd($request->city);
        $distributor = new Distributor();
        if ($request->city) {
            $distributor = $distributor->where('city_id' , $request->city);
        }
        $distributor = $distributor->get();
        return  response()->json(['distributor'=>$distributor]);
    }

}
