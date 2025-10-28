<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\UOM;
use App\Http\Requests\StoreUOMRequest;
use App\Http\Requests\UpdateUOMRequest;
use Illuminate\Http\Request;

class UOMController extends Controller
{


    public function __construct()
    {
        $this->page = 'pages.Products.UOM.';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,UOM $uom)
    {
       $uom =  $uom::status()->get();
       if ($request->ajax()):
           return  view($this->page.'UOMListAjax',compact('uom'));
        endif;
       return  view($this->page.'UOMList');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return  view($this->page.'AddUOM');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUOMRequest $request)
    {

       UOM::create($request->all());
       return response()->json(['success' => 'Brand created successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return  view($this->page.'AddCategory');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(UOM $uom)
    {
        return  view($this->page.'EditUOM',compact('uom'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUOMRequest $request, UOM $uom)
    {
        $uom = $uom->update($request->all());
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
        UOM::where('id',$id)->update(['status'=>0]);
        return response()->json(['success'=>'Deleted Successfully!']);
    }
}
