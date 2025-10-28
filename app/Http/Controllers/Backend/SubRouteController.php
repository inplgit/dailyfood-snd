<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubrouteRequest;
use App\Http\Requests\UpdateRouteRequest;
use App\Helpers\MasterFormsHelper;
use Illuminate\Http\Request;
use App\Models\SubRoutes;
use App\Models\Route;


class SubRouteController extends Controller
{
    public $master;
    public $page;
    public function __construct()
    {
        $this->page = 'pages.Routes.SubRoute.';
        $this->master = new MasterFormsHelper();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,SubRoutes $subroutes)
    {

        $subroutes =  $subroutes::status()->get();
       if ($request->ajax()):
           return  view($this->page.'SubRouteListAjax',compact('subroutes'));
        endif;
       return  view($this->page.'SubRouteList');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $route = Route::status()->get();
        return  view($this->page.'AddSubRoute',compact('route'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSubrouteRequest $request)
    {
        SubRoutes::create($request->all());
       return response()->json(['success' => 'Route created successfully.']);
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
    public function edit(SubRoutes $subroutes ,$id)
    {

        $route = Route::status()->get();
        $subroutes= SubRoutes::where('id',$id)->first();
        return  view($this->page.'EditSubRoute',compact('subroutes','route'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request , $id)
    {
        $subroutes = SubRoutes::find($id)->update($request->all());
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
        SubRoutes::where('id',$id)->update(['status'=>0]);
        return response()->json(['success'=>'Deleted Successfully!']);
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

    public function GetTsoByDistributor(Request $request)
    {
        $distributor = $request->distribuotr_id;
        $tso = $this->master->get_all_tso_by_distributor_id($distributor);
        return  response()->json(['tso'=>$tso]);
    }
    public function GetRouteBYTSO(Request $request)
    {
        $tso_id = $request->tso_id;
        $route = $this->master->get_all_route_by_tso($tso_id);
        return  response()->json(['route'=>$route]);
    }

    public function route_tso_wise(Request $request ,Route $route)
    {
      $day = $request->day;
      $ids = $request->ids;

      foreach($day as $key => $row):
      $route->where('id',$ids[$key])->update(['day'=>$day[$key]]);
      endforeach;

      return redirect()->back()->with('success', 'Routes Updated');
    }

}
