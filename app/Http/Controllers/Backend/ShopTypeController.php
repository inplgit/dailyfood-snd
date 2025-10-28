<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\ShopType;
use App\Http\Requests\StoreShopTypeRequest;
use App\Http\Requests\UpdateShopTypeRequest;
use Illuminate\Http\Request;

class ShopTypeController extends Controller
{


    public function __construct()
    {
        $this->page = 'pages.Shop.ShopType.';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,ShopType $shop_type)
    {
       $shop_type =  $shop_type::status()->get();
       if ($request->ajax()):
           return  view($this->page.'ShopTypeListAjax',compact('shop_type'));
        endif;
       return  view($this->page.'ShopTypeList');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return  view($this->page.'AddShopType');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreShopTypeRequest $request)
    {

       ShopType::create($request->all());
       return response()->json(['success' => 'Shop Type created successfully.']);
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
    public function edit(ShopType $shoptype)
    {
       
        return  view($this->page.'EditShopType',compact('shoptype'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateShopTypeRequest $request, ShopType $shoptype)
    {
        $shoptype = $shoptype->update($request->all());
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
        ShopType::where('id',$id)->update(['status'=>0]);
        return response()->json(['success'=>'Deleted Successfully!']);
    }
}
