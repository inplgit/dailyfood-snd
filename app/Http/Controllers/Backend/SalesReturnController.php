<?php

namespace App\Http\Controllers\Backend;

use App\Models\Product;
use App\Models\Stock;
use App\Models\SalesReturn;
use App\Models\SalesReturnData;
use App\Models\SaleOrderReturnDetail;
use App\Models\ProductFlavour;
use App\Models\ProductPrice;
use App\Models\SaleOrder;
use App\Models\User;
use App\Models\SaleOrderData;
use App\Models\SaleOrderReturn;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\MasterFormsHelper;
use Auth;

class SalesReturnController extends Controller
{


    public function __construct()
    {
        $this->page = 'pages.SalesReturn.';
        $this->master = new MasterFormsHelper();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index(Request $request)
{
     //     $sales_return = SalesReturn::all();
    $sales_return = $this->master->get_sales_orders_return($request);
    if ($request->ajax()) {
        return view($this->page . 'SalesReturnListAjax', compact('sales_return'));
    }

    return view($this->page . 'SalesReturnList');
}


 public function index_return(Request $request)
{
    $sales_return =  SaleOrderReturn::with('returnDetails')
     ->where('status', 1)
        ->when($request->distributor_id, function ($query) use ($request) {
            $query->where('distributor_id', $request->distributor_id);
        })
        ->when($request->tso_id, function ($query) use ($request) {
            $query->whereHas('tso', function ($q) use ($request) {
                $q->where('id', $request->tso_id);
            });
        })
        ->when($request->from && $request->to, function ($query) use ($request) {
            $query->whereBetween('return_date', [$request->from, $request->to]);
        })
        ->get();

    if ($request->ajax()) {
        return view($this->page . 'SalesOrderReturnListAjaxKpo', compact('sales_return'));
    }

    return view($this->page . 'SalesOrderReturnListKpo');
}




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        if ($request->ajax()) :
            $invoice_no = $request->so_no;
            $so_data = SaleOrder::where('invoice_no', $invoice_no)->first();


            return view($this->page . 'AddSalesReturnAjax', compact('so_data'));
        endif;
        return view($this->page . 'AddSalesReturn');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        DB::beginTransaction();
        try {
            $sales_return = new SalesReturn();
            $sales_return->so_id = $request->so_id;
            $main_discount = $sales_return->SalesOrder->discount_percent;
            $sales_return->voucher_no = $sales_return->UniqueNo();
            $sales_return->description = $request->description;
            $sales_return->user_id  = Auth::user()->id;
            $sales_return->save();
            $id = $sales_return->id;

            $total_amount = 0;
            foreach ($request->qty as $key => $row) :
                if ($row > 0) :
                    $sales_return_data = new SalesReturnData();

                    // calc
                    $sales_order_data  = SaleOrderData::where('id', $request->input('so_data_id')[$key])->first();
                    $rate = $sales_order_data->rate;
                    $amount = $rate * $row;
                    $discount = $sales_order_data->discount;
                    $tax = $sales_order_data->tax_percent;
                    ($discount > 0) ? $discount_amount = ($amount / 100) * $discount :  $discount_amount = 0;
                    $amount = $amount - $discount_amount;


                    ($tax > 0) ? $tax_amount = ($amount / 100) * $tax :  $tax_amount = 0;
                    $amont = $amount + $tax_amount;
                    //

                    $sales_return_data->sales_return_id = $id;
                    $sales_return_data->sales_order_data_id  = $request->input('so_data_id')[$key];
                    $sales_return_data->qty = $row;
                    $sales_return_data->save();
                    $total_amount += $amount;
                endif;
            endforeach;


            ($discount_amount > 0) ? $main_discount_amount = ($total_amount / 100) * $main_discount : $main_discount_amount = 0;
            $total_amount = $total_amount - $main_discount_amount;

            $sales_return->amount = $total_amount;
            $sales_return->save();
            DB::commit();
            return response()->json(['success' => 'Return Successfully Saved']);
        } catch (Exception $th) {
            DB::rollBack();
            return $this->sendError("Server Error!", ['error' => $th->getMessage()]);
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
        $so = SalesReturn::find($id);
        return view($this->page.'viewSalesReturn',compact('so'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return  view($this->page . 'EditProduct', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        return;
        $product = $product->update($request->all());
        return response()->json(['success' => 'Updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  
//     public function destroy($id)
// {
   
//     try {
//          $SalesReturn = SalesReturn::where('id', $id)->update(['status'=> 0]);
     
        
//         return response()->json([
//             'success' => 'SalesReturn Order Deleted Successfully'
//         ]);
        
//     } catch (\Exception $e) {
//         return response()->json([
//             'error' => 'Error deleting record: ' . $e->getMessage()
//         ], 500);
//     }
// }


public function destroy($id)
{
    try {
        SaleOrderReturn::where('id', $id)->update(['status' => 0]);

        return redirect()->back()->with('success', 'SalesReturn Order Deleted Successfully');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error deleting record: ' . $e->getMessage());
    }
}


    public function sales_return_list(Request $request, $excution)
    {
        // $sales_return = SalesReturn::where('excute', false)->get();

        $sales_return = $this->master->get_sales_orders_return_excute($request);

        if ($request->ajax()) :
            return view($this->page . 'SalesReturnListAjax', compact('sales_return', 'excution'));
        endif;
        return view($this->page . 'SalesReturnList', compact('excution'));
    }

    public function sales_return_list_shop_wise(Request $request, $excution)
    {
        // Get distributor IDs linked to logged-in user
        $userDistributorIds = User::find(Auth::id())
            ->distributors()
            ->where('status', 1)
            ->pluck('distributors.id');

        $sales_return = SaleOrderReturn::with('returnDetails')
            ->when($request->distributor_id, function ($query) use ($request) {
                $query->where('distributor_id', $request->distributor_id);
            }, function ($query) use ($userDistributorIds) {
                // Default: only show distributors for logged-in user
                $query->whereIn('distributor_id', $userDistributorIds);
            })
            ->when($request->tso_id, function ($query) use ($request) {
                $query->whereHas('tso', function ($q) use ($request) {
                    $q->where('id', $request->tso_id);
                });
            })
            ->when($request->from && $request->to, function ($query) use ($request) {
                $query->whereBetween('return_date', [$request->from, $request->to]);
            })
            ->where('excecution', 0)
            ->where('status', 1)
            ->get();
        // $sales_return = SalesReturn::where('excute', false)->get();

        // $sales_return = $this->master->get_sales_orders_return_shop_wise_excute($request);


      //  dd($sales_return);

        if ($request->ajax()) :
            return view($this->page . 'SalesOrderReturnListAjax', compact('sales_return', 'excution'));
        endif;
        return view($this->page . 'SalesOrderReturnList', compact('excution'));
    }

 public function sales_return_list_shop($id)
{
    $so = SaleOrderReturn::with([
    'returnDetails.product.retailPrice', 
    'shop',
    'distributor',
    'tso'
])->where('id', $id)->firstOrFail();



    return view($this->page . 'viewSalesReturnOrder', compact('so'));
}

public function sales_return_execution_submit(Request $request)
{

 
    DB::beginTransaction();
    try {
        $data = SaleOrderReturn::whereIn('id', $request->id)
            ->where('excecution', false)
            ->get();

        foreach ($data as $return) {

            // Mark execution
            SaleOrderReturn::where('id', $return->id)
                ->update(['excecution' => 1]);

            // Sirf Fresh wale hi pick karo
            $salesorderreturnget = SaleOrderReturnDetail::where('sale_order_return_id', $return->id)
                ->where('reason', 'Fresh')
                ->get();

    

            foreach ($salesorderreturnget as $row) {

               
                $flavourId = ProductFlavour::where('product_id', $row->product_id)
                    ->value('id'); 

                // ✅ product_prices table se trade_price
                $tradePrice = ProductPrice::where('product_id', $row->product_id)
                    ->where('uom_id', 1) // yahan aap $row->uom_id bhi use kar sakte ho agar details me ho
                    ->value('trade_price');

                // agar record na mile to 0 set kar de
                $tradePrice = $tradePrice ?? 0;

                // ✅ calculate amount
                $amount = $row->quantity * $tradePrice;

                $stock = new Stock();
                $stock->voucher_no = $return->return_no;
                $stock->voucher_date = date('Y-m-d');
                $stock->product_id = $row->product_id;
                $stock->flavour_id = $flavourId;
                $stock->uom_id = 1;
                $stock->distributor_id = $return->distributor_id;
                $stock->stock_type = 4;
                $stock->qty = $row->quantity;
                $stock->parent_id = $return->id;
                $stock->child_id = $row->id;
                $stock->stock_received_type = 1;
                $stock->remarks = $row->reason;

                // ✅ new columns
                $stock->rate = $tradePrice; 
                $stock->amunt = $amount;

                $stock->save();
            }
        }

        DB::commit();
        return response()->json(['success' => 'Successfully executed.']);

    } catch (Exception $th) {
        DB::rollBack();
        return response()->json(['error' => 'Oops! There might be an error: ' . $th->getMessage()]);
    }
}


    public function viewPaymentRecoveryDetail($id)
    {
        return view($this->page.'viewPaymentRecoveryDetail');
    }


}
