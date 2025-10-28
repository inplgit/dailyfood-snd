<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
class CityController extends Controller
{

    

    public function __construct()
    {
        $this->page = 'pages.city.';
    }
      
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $city = City::where('status', 1)->get();
        // dd($city);
        if ($request->ajax()):
            return view($this->page.'TableData',compact('city'));
        endif;
        return view($this->page.'IndexCity');
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return  view($this->page.'AddCity');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

 // Get all the data from the request
 $request = $request->all();
 $request['out_of_stock_quantity'] = isset($request['out_of_stock_quantity']) ? $request['out_of_stock_quantity'] : '0';
 $city = City::create($request);


        return response()->json(['success'=>'City Created Successfully']);
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
    public function edit($id)
    {


        $city = City::findOrFail($id);
        return  view($this->page.'EditCity', compact('city'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request = $request->all();
        $request['out_of_stock_quantity'] = isset($request['out_of_stock_quantity']) ? $request['out_of_stock_quantity'] : '0';

       

        $city = City::findOrFail($id);
        $city = $city->update($request);
        return response()->json(['success'=>'City Updated Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $city = City::where('id', $id)->update(['status'=> 0]);
        return response()->json(['success'=>'City Deleted Successfully']);
    }
}
