<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Rack;

use App\Http\Controllers\Controller;
use App\Models\AssignRack;
use App\Models\RackScan;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;
// use App\Http\Controllers\API\V1\BaseController as BaseController;

class RackController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'rack_code' => 'required',
            'bar_code' => 'required',
        ]);

        $rack = new Rack;
        $rack->rack_code  = $request->rack_code;
        $rack->bar_code  =$request->bar_code;
        $rack->status =1;
        $rack->save();
        return $this->sendResponse([], 'Rack Successfully Inserted.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Rack  $rack
     * @return \Illuminate\Http\Response
     */
    public function show(Rack $rack)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Rack  $rack
     * @return \Illuminate\Http\Response
     */
    public function edit(Rack $rack)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Rack  $rack
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rack $rack)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rack  $rack
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rack $rack)
    {
        //
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rack  $rack
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {

        $rack = Rack::where('bar_code',$request->bar_code)->status()->count();
        return $rack;


    }
    public function rack_list(Request $request)
    {

        $tso_id = Auth::user()->tso->id;

        $latestRackScans = DB::table('rack_scans as rs1')
        ->select('rs1.*')
        // ->where('rs1.assign_status', 1)
        ->whereRaw('rs1.created_at = ( SELECT MAX(rs2.created_at)  FROM rack_scans as rs2 WHERE rs2.rack_id = rs1.rack_id ORDER BY rs2.created_at DESC LIMIT 1)');


        $rack =  Rack::
        // leftJoin('assign_racks', 'assign_racks.rack_id', '=', 'racks.id')
        leftJoin('assign_racks', function($join) {
            $join->on('racks.id', '=', 'assign_racks.rack_id')
            ->where('assign_racks.assign_status', 1);
            // ->where(function($query) {
            //     $query->where('assign_racks.assign_status', 0)
            //           ->orWhereNull('assign_racks.assign_status');
            // });
        })
        // ->leftJoin('rack_scans', function($join) {
        //     $join->on('rack_scans.rack_id' , 'assign_racks.rack_id')
        //     ->orderBy('rack_scans.created_at' , 'DESC')
        //     ->latest('rack_scans.id')
        //     ;
        // })
        ->leftJoinSub($latestRackScans, 'rack_scans', function($join) {
            $join->on('assign_racks.rack_id', '=', 'rack_scans.rack_id');
        })
        ->leftjoin('shops' , 'shops.id' , 'assign_racks.shop_id')
        ->leftjoin('tso' , 'tso.id' , 'assign_racks.tso_id')
        ->where('racks.status', 1)
        // ->where(function($query) {
        //     $query->where('assign_racks.assign_status', 0)
        //           ->orWhereNull('assign_racks.assign_status');
        // })
        ->select('racks.id as rack_id' , 'racks.rack_code' , 'racks.bar_code' ,'tso.id as tso_id','tso.name as tso_name','shops.id as shop_id','shops.company_name as shop_name' , 'assign_racks.created_at',
        DB::raw('CASE WHEN assign_racks.assign_status = 0 OR assign_racks.assign_status IS NULL THEN true ELSE false END as assign_status'),
        DB::raw('CASE WHEN assign_racks.assign_status = 1 and assign_racks.tso_id = '. $tso_id .' THEN true ELSE false END as claim_status'),
        DB::raw('CASE WHEN rack_scans.rack_status = 0 THEN "Not Available" WHEN rack_scans.rack_status IS NULL THEN "Not Assign" ELSE "Available" END as scan_status')
        // ,'latest_rack_scans.rack_status'
        )
        ->groupBy('racks.id')
        ->get();

        // dd($rack->toArray());
        return $this->sendResponse($rack,'Target Fetch...');

    }

    public function assign_rack(Request $request)
    {

        // $request->validate([
        //     'shop_id' => 'required',
        //     'rack_id' => 'required',
        // ]);

        $validator = Validator::make($request->all(), [
            'shop_id' => ['required','integer','not_in:0'],
            'rack_id' => ['required','integer','not_in:0'],
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // dd('chaksd');


        DB::beginTransaction();
        try {

            $rack_id = $request->rack_id;
            $shop_id = $request->shop_id;
            $tso_id = Auth::user()->tso->id;
            $count = Rack::Join('assign_racks', 'assign_racks.rack_id', '=', 'racks.id')
            ->where('assign_racks.rack_id' , $rack_id)
            ->where('assign_racks.assign_status', 1)
            ->where('racks.status', 1)
            ->latest('assign_racks')
            ->count();
            // dd($count);
            if ($count > 0) {
                return $this->sendError('This Rack already assigned.');

            }
            $assign = new AssignRack();
            $assign->tso_id = $tso_id;
            $assign->shop_id = $shop_id;
            $assign->rack_id = $rack_id;
            $assign->assign_status = 1;
            $assign->save();

            DB::commit();
            return $this->sendResponse($assign , 'Assign Rack successfully.');


        } catch (Exception $th) {
            DB::rollBack();
            return $this->sendError("Server Error!",['error'=> $th->getMessage()]);
        }        // dd($count);
    }

    public function reclaim_rack(Request $request)
    {

        // $request->validate([
        //     'shop_id' => 'required',
        //     'rack_id' => 'required',
        // ]);

        $validator = Validator::make($request->all(), [
            'shop_id' => ['required','integer','not_in:0'],
            'rack_id' => ['required','integer','not_in:0'],

        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // dd('chaksd');


        DB::beginTransaction();
        try {

            $rack_id = $request->rack_id;
            $shop_id = $request->shop_id;
            $remarks = $request->remarks;
            $tso_id = Auth::user()->tso->id;

            $count = Rack::Join('assign_racks', 'assign_racks.rack_id', '=', 'racks.id')
            ->where('assign_racks.rack_id' , $rack_id)
            ->where('assign_racks.tso_id' , $tso_id)
            ->where('assign_racks.shop_id' , $shop_id)
            ->where('assign_racks.assign_status', 1)
            ->where('racks.status', 1)
            ->latest('assign_racks')
            ->count();
            // dd($count);
            if ($count == 0) {
                return $this->sendError('No Data Fount.');
            }
            $assign = AssignRack::where(['rack_id' => $rack_id , 'tso_id' => $tso_id , 'assign_status' => 1])->first();

            $assign->remarks = $remarks;
            $assign->assign_status = 0;
            $assign->save();

            DB::commit();
            return $this->sendResponse($assign , 'Reclaim rack successfully.');


        } catch (Exception $th) {
            DB::rollBack();
            return $this->sendError("Server Error!",['error'=> $th->getMessage()]);
        }        // dd($count);
    }

    public function scan_rack(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_id' => 'required',
            'rack_id' => 'required',

        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();
        try {
            $tso_id = Auth::user()->tso->id;

            $scan = new RackScan();
            $scan->rack_id = $request->rack_id;
            $scan->shop_id = $request->shop_id;
            $scan->tso_id = $tso_id;
            $scan->rack_status  = $request->rack_status;
            $scan->save();

            DB::commit();
            return $this->sendResponse($scan , 'Rack Status Save successfully.');

         }
        catch (Exception $th) {
            DB::rollBack();
            return $this->sendError("Server Error!",['error'=> $th->getMessage()]);
        }

    }

}
