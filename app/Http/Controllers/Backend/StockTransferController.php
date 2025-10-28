<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\MasterFormsHelper;
use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StockTransferController extends Controller
{

    public function __construct()
    {
        $this->page = "pages.Distributor.Stocktransfer.";
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $distributors = MasterFormsHelper::get_all_distributor_user_wise_pluck();
        if ($request->ajax()):
            $stock =   Stock::status()->where('stock_type',5)->whereIn('distributor_id',$distributors)->groupBy('voucher_no')->get();
            return  view($this->page.'StockTransferListAjax',compact('stock'));
        endif;
        return view($this->page. 'StockTransferList',compact('distributors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->page . "AddStockTransfer");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     DB::beginTransaction();
    //     try {

    //         $validatedData =  Validator::make($request->all(),[
    //             'distributor_id' => 'required',
    //             'distributor_id_to' => 'required',
    //             'voucher_date' => 'required',
    //             'product_id.*' => 'required',
    //             'qty.*' => 'required',
    //         ]);

    //         if ($validatedData->fails()):

    //         return response()->json(['error' => $validatedData->errors()]);
    //         endif;

    //       $voucher_no =    MasterFormsHelper::stock_unique_no(2);
    //       $voucher_no = 'st-'.$voucher_no;
    //         foreach ($request->product_id as $key => $product_id):
    //             $stock = MasterFormsHelper::get_InStock($request->product_id[$key] , $item->flavour_id[$key] , $item->uom_id[$key] , $request->distributor_id, $request->qty[$key]);
    //             if ($stock == false):

    //                 DB::rollback();
    //                 return response()->json(['catchError' => 'No Stock Avaiable']);
    //                endif;
    //             Stock::create([
    //                 "distributor_id"      => $request->distributor_id,
    //                 "distributor_sole"      => $request->distributor_id_to,
    //                 "voucher_no"      => $voucher_no,
    //                 "stock_received_type" => 1,
    //                 "voucher_date"        => $request->voucher_date,
    //                 "stock_type"          => 5,
    //                 "remarks"             => $request->remarks,
    //                 "product_id"          => $request->product_id[$key],
    //                 "qty"                 => $request->qty[$key],
    //             ]);


    //             Stock::create([
    //                 "distributor_id"      => $request->distributor_id_to,
    //                 "voucher_no"      => $voucher_no,
    //                 "stock_received_type" => 2,
    //                 "voucher_date"        => $request->voucher_date,
    //                 "stock_type"          => 2,
    //                 "remarks"             => $request->remarks,
    //                 "product_id"          => $request->product_id[$key],
    //                 "qty"                 => $request->qty[$key],
    //             ]);

    //         endforeach;
    //         DB::commit();
    //         return response()->json(['success' => 'Stock Add Successfully']);
    //     } catch (Exception $th) {
    //         //throw $th;
    //         DB::rollback();
    //         return response()->json(['catchError' => $th->getMessage()]);
    //     }
    // }

  public function store(Request $request)
{
    DB::beginTransaction();
    try {
        // Validation
        $validatedData = Validator::make($request->all(), [
            'distributor_id'     => 'required',
            'distributor_id_to'  => 'required',
            'voucher_date'       => 'required|date',
            'product_id.*'       => 'required|integer',
            'flavour_id.*'       => 'required|integer',
            'uom_id.*'           => 'required|integer',
            'qty.*'              => 'required|numeric|min:1',
        ]);

        if ($validatedData->fails()) {
            return response()->json(['error' => $validatedData->errors()]);
        }

        // Generate voucher no
        $voucher_no = MasterFormsHelper::stock_unique_no(2);
        $voucher_no = 'st-' . $voucher_no;

        foreach ($request->product_id as $key => $product_id) {
            // Check stock availability
            $stock = MasterFormsHelper::get_InStock(
                $request->product_id[$key],
                $request->flavour_id[$key],
                $request->uom_id[$key],
                $request->distributor_id,
                $request->qty[$key]
            );

            if ($stock === false) {
                DB::rollBack();
                return response()->json(['catchError' => 'No Stock Available']);
            }

            // Deduct from distributor stock
            Stock::create([
                "distributor_id"       => $request->distributor_id,
                "distributor_sole"     => $request->distributor_id_to,
                "voucher_no"           => $voucher_no,
                "stock_received_type"  => 1,
                "voucher_date"         => $request->voucher_date,
                "stock_type"           => 5,
                "remarks"              => $request->remarks,
                "product_id"           => $request->product_id[$key],
                "flavour_id"           => $request->flavour_id[$key],
                "uom_id"               => $request->uom_id[$key],
                "qty"                  => $request->qty[$key],
            ]);

            // Add to receiver distributor stock
            Stock::create([
                "distributor_id"       => $request->distributor_id_to,
                "voucher_no"           => $voucher_no,
                "stock_received_type"  => 2,
                "voucher_date"         => $request->voucher_date,
                "stock_type"           => 2,
                "remarks"              => $request->remarks,
                "product_id"           => $request->product_id[$key],
                "flavour_id"           => $request->flavour_id[$key],
                "uom_id"               => $request->uom_id[$key],
                "qty"                  => $request->qty[$key],
            ]);
        }

        DB::commit();
        return response()->json(['success' => 'Stock Added Successfully']);
    } catch (Exception $th) {
        DB::rollBack();
        return response()->json(['catchError' => $th->getMessage()]);
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
       $stock =  Stock::status()->where('voucher_no',$id)->where('stock_type',5)->get();
      return view($this->page. 'viewTransfer',compact('stock'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $stock =  Stock::status()->where('voucher_no',$id)->where('stock_type',5)->get();
        return view($this->page. 'EditStockTransfer',compact('stock'));
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $stock =  Stock::status()->where('voucher_no',$id)->where('stock_type',2)->get();

            foreach ($stock as $key=>$row):

                $stock = MasterFormsHelper::get_InStock($row->product_id , $row->flavour_id , $row->sale_type , $row->distributor_id, $row->qty);
                if ($stock == false):
                    DB::rollback();
                    return response()->json(['catchError' => 'Can Not Deleted Due To Stock']);
                endif;

            endforeach;
            $stock =  Stock::status()->where('voucher_no',$id)->update(['status'=>0]);
            DB::commit();
            return response()->json(['success' => 'Stock Deleted Successfully']);
        } catch (Exception $th) {
            //throw $th;
            DB::rollback();
            return response()->json(['catchError' => $th->getMessage()]);
        }
    }
}
