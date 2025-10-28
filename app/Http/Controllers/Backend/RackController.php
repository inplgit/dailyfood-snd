<?php

namespace App\Http\Controllers\Backend;

use App\Models\Rack;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RackController extends Controller
{
    protected $page;

    public function __construct()
    {
        $this->page = 'pages.rack.';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rack =  Rack::status()->get();
       if ($request->ajax()):
           return  view($this->page.'rackListAjax',compact('rack'));
        endif;
       return  view($this->page.'index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->page.'create');
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
        return response()->json(['success' => 'Rack created successfully.']);
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
        return  view($this->page.'EditRack',compact('rack'));
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
        $rack = $rack->update($request->all());
        return response()->json(['success' => 'Updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rack  $rack
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Rack::where('id',$id)->update(['status'=>0]);
        return response()->json(['success'=>'Deleted Successfully!']);
    }


    public function issue_rack(Request $request)
    {
        $racks = Rack::get();
        return view('pages.rack.issue_rack' , compact('racks'));
    }

    public function issue_rack_to_shop(Request $request)
    {
        dd($request);
    }


}
