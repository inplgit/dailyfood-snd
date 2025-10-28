<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePriceTypeRequest;
use App\Http\Requests\UpdatePriceTypeRequest;
use App\Models\PriceType;
use Illuminate\Http\Request;

class PriceTypeController extends Controller
{

    protected $page;

    public function __construct()
    {
        $this->page = 'pages.Shop.PriceType.';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, PriceType $type)
    {
        $type =  $type::status()->get();
        if ($request->ajax()) :
            return  view($this->page . 'PriceTypeListAjax', compact('type'));
        endif;
        return  view($this->page . 'PriceTypeList');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return  view($this->page . 'AddPriceType');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePriceTypeRequest $request)
    {
        PriceType::create($request->all());
        return response()->json(['success' => 'Price Type created successfully.']);
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
    public function edit(PriceType $priceType)
    {
        return  view($this->page . 'EditPriceType', compact('priceType'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePriceTypeRequest $request, PriceType $priceType)
    {
        $priceType = $priceType->update($request->all());
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
        PriceType::where('id', $id)->update(['status' => 0]);
        return response()->json(['success' => 'Deleted Successfully!']);
    }
}
