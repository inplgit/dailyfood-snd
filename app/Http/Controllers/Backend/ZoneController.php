<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreZoneRequest;
use App\Http\Requests\UpdateZoneRequest;
use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{

    protected $page;

    public function __construct()
    {
        $this->page = 'pages.Distributor.Zone.';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Zone $zone)
    {
        $zone =  $zone::status()->get();


        if ($request->ajax()) :
            return  view($this->page . 'ZoneListAjax', compact('zone'));
        endif;
        return  view($this->page . 'ZoneList');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return  view($this->page . 'AddZone');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreZoneRequest $request)
    {


        Zone::create($request->all());
        return response()->json(['success' => 'Zone created successfully.']);
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
    public function edit(Zone $zone)
    {
        return  view($this->page . 'EditZone', compact('zone'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateZoneRequest $request, Zone $zone)
    {
        $zone = $zone->update($request->all());
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
        Zone::where('id', $id)->update(['status' => 0]);
        return response()->json(['success' => 'Deleted Successfully!']);
    }
}
