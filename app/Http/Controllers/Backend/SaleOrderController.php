<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleOrderRequest;
use App\Http\Requests\UpdateSaleOrderRequest;
use App\Models\Distributor;
use App\Models\Product;
use App\Models\SaleOrder;
use App\Models\SaleOrderData;
use App\Models\ShopOutsanding;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\TSO;
use App\Helpers\MasterFormsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleOrderController extends Controller
{
    public function __construct()
    {
        $this->page = 'pages.SalesOrder.';
        $this->master = new MasterFormsHelper();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


        $sales = $this->master->get_sales_orders($request);

        if ($request->ajax()):
            return view($this->page.'TableData',compact('sales'));
        endif;
        return view($this->page.'IndexSale');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $invoice_no = SaleOrder::UniqueNo();
        $distributors = Distributor::where('status', 1)->get();
        $tsos = TSO::all();
        // $shops = Shop::Status()->get();
        // dd($shops , 'asdsad');
        $products = Product::status()->get();
        return  view($this->page.'CreateSaleOrder', compact('distributors', 'tsos', 'products', 'invoice_no'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSaleOrderRequest $request)
    {
        // dd($request->all());
        $requestData = $request->all();
        try {
            $invoice_no = SaleOrder::UniqueNo();

            $saleOrder =  SaleOrder::create([
                'distributor_id' => $request->distributor_id ?? 0,
                'tso_id' => $request->tso_id ?? 0,
                'route_id' => $request->route_id ?? 0,
                'shop_id' => $request->shop_id ?? 0,
                'user_id' => auth()->user()->id ?? 0,

                'invoice_no' => $invoice_no ?? 0,
                'dc_no' => $request->dc_no ?? 0,
                'lpo_no' => $request->lpo_no ?? 0,
                'dc_date' => $request->dc_date ?? '0000-00-00',
                'excecution' => $request->excecution ?? 0,
                'excecution_date' => $request->excecution_date ?? null,
                'payment_type' => $request->payment_type ?? 0,
                'total_carton' => $request->total_carton ?? 0,
                'total_pcs' => $request->total_pcs ?? 0,
                'cost_center' => $request->cost_center ?? 0,
                'notes' => $request->notes ?? 0,
                'transport_details' => $request->transport_details ?? 0,
                'discount_percent' => $request->discount_percent ?? 0,
                'discount_amount' => $request->discount_amount,
                'tax_applied' => $request->tax_applied ?? 0,
                'pending_amount' => $request->pending_amount ?? 0,
                'total_amount' => $request->total_amount ?? 0,
                'products_subtotal' => $request->products_subtotal ?? 0,
                'old_receivable' => $request->old_receivable ?? 0,
                'freight_charges' => $request->freight_charges ?? 0,
                'delivery_date' => $request->delivery_date ?? '0000-00-00',

            ]);

            foreach ($request->product_id as $key => $value) {
                if ($request['scheme_id'][$key] != null && $request['scheme_id'][$key] != '' ) {
                    $scheme = explode(',',$request['scheme_id'][$key]);
                    $scheme_id = $scheme[0];
                    $scheme_data_id = $scheme[1];
                }
 		if ($request['scheme_id_pcs'][$key] != null && $request['scheme_id_pcs'][$key] != '' ) {
                    $scheme = explode(',',$request['scheme_id_pcs'][$key]);
                    $scheme_id_pcs = $scheme[0];
                    $scheme_data_id_pcs = $scheme[1];
                }


                if (isset($request['scheme_pcs'][$key]) && $request['scheme_pcs'][$key] != null) {
                    $scheme_pcs = $request['scheme_pcs'][$key];
                }
                $saleOrderData = SaleOrderData::create([
                    'so_id' => $saleOrder->id ?? 0,
                    'product_id' => $requestData['product_id'][$key] ?? 0,
                    'flavour_id' => $requestData['flavour_id'][$key] ?? 0,
                    'sale_type' => $requestData['sale_type'][$key] ?? 0,
                    'rate' => $requestData['rate'][$key] ?? 0,
                    'qty' => $requestData['qty'][$key] ?? 0,
                    'discount' => $requestData['data_discount'][$key] ?? 0,
                    'discount_amount' => $requestData['data_discount_amount'][$key] ?? 0,
                    'tax_percent' => $requestData['data_tax_percent'][$key] ?? 0,
                    'tax_amount' => $requestData['data_tax_amount'][$key] ?? 0,
                    'trade_offer_amount' => $requestData['trade_offer_amount'][$key] ?? 0,
                     'scheme_id' => $scheme_id ?? 0,
                    'scheme_data_id' => $scheme_data_id ?? 0,
                    'scheme_id_pcs' => $scheme_id_pcs ?? 0,
                    'scheme_data_id_pcs' => $scheme_data_id_pcs ?? 0,
                    'scheme_amount' => $requestData['scheme_amount'][$key] ?? 0,
                    'scheme_data_pcs' => $scheme_pcs ?? 0,
                    'total' => $requestData['data_total'][$key] ?? 0,
                ]);
            }
            return response()->json(['success'=>'Sale Order Created Successfully']);
        } catch (\Throwable $th) {
            return response()->json(['error'=>'Oops! There might be a error '. $th->getMessage()]);
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
       $so= SaleOrder::find($id);
      
       $scheme_Pcs = DB::table('sale_orders as so')
       ->leftJoin('sale_order_data as sod', 'sod.so_id', '=', 'so.id')
       ->leftJoin('scheme_product_pcs as sp', 'sp.id', '=', 'sod.scheme_id_pcs')
       ->leftJoin('scheme_product_data_pcs as spd', 'spd.scheme_id', '=', 'sp.id') 
       ->leftJoin('products as p', 'p.id', '=', 'spd.product_id') 
       ->where('so.id', $id)
       ->select('p.product_name', 'spd.scheme_Pcs','sod.scheme_data_pcs')
       ->get();
   
     
      
   
       return  view($this->page.'viewSalesOrder',compact('so','scheme_Pcs'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $record = SaleOrder::findOrFail($id);
        // dd($record->saleOrderData);
        $distributors = Distributor::where('status', 1)->get();
        $tsos =  $data = TSO::where('distributor_id', $record->distributor_id)->get();

        //$shops = Shop::where('status', 1)->where('distributor_id',$record->distributor_id)->where('tso_id',$record->tso_id)->get();
$shops = Shop::join('shop_tso', 'shop_tso.shop_id', '=', 'shops.id')
    ->where('shops.status', 1)
    ->where('shops.distributor_id', $record->distributor_id)
    ->where('shop_tso.tso_id', $record->tso_id)
    ->select('shops.*')
    ->get();

        $products = Product::status()->get();
        $shceme_products = Product::statusScheme()->get();

        

        return  view($this->page.'EditSaleOrder', compact('record', 'distributors', 'tsos', 'products', 'shops','shceme_products'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSaleOrderRequest $request, $id)
    {
        // dd($request, $id);
        $requestData = $request->all();
        DB::beginTransaction();
        try {
            $saleOrder = SaleOrder::findOrfail($id);
            $saleOrder =  $saleOrder->update([
                'distributor_id' => $request->distributor_id ?? 0,
                'tso_id' => $request->tso_id ?? 0,
                'route_id' => $request->route_id ?? 0,
                'shop_id' => $request->shop_id ?? 0,
                'user_id' => auth()->user()->id ?? 0,
                'invoice_no' => $request->invoice_no ?? 0,
                'dc_no' => $request->dc_no ?? 0,
                'lpo_no' => $request->lpo_no ?? 0,
                'dc_date' => $request->dc_date ?? '0000-00-00',
                'excecution' => $request->excecution ?? 0,
                'excecution_date' => $request->excecution_date ?? null,
                'payment_type' => $request->payment_type ?? 0,
                'total_carton' => $request->total_carton ?? 0,
                'total_pcs' => $request->total_pcs ?? 0,
                'cost_center' => $request->cost_center ?? 0,
                'notes' => $request->notes ?? 0,
                'transport_details' => $request->transport_details ?? 0,
                'discount_percent' => $request->discount_percent ?? 0,
                'discount_amount' => $request->discount_amount,
                'tax_applied' => $request->tax_applied ?? 0,
                'pending_amount' => $request->pending_amount ?? 0,
                'total_amount' => $request->total_amount ?? 0,
                'products_subtotal' => $request->products_subtotal ?? 0,
                'old_receivable' => $request->old_receivable ?? 0,
                'freight_charges' => $request->freight_charges ?? 0,
                'delivery_date' => $request->delivery_date ?? '0000-00-00',

            ]);
            SaleOrderData::whereIn('id',explode(',',$request->remove_id))->delete();
            foreach ($request->product_id as $key => $item) {
                $saleOrderData = SaleOrderData::find($requestData['sale_order_data_id'][$key] ?? 0);

                if ($request['scheme_id'][$key] != null && $request['scheme_id'][$key] != '' ) {
                    $scheme = explode(',',$request['scheme_id'][$key]);
                    $scheme_id = $scheme[0];
                    $scheme_data_id = $scheme[1];
                }

                if ($saleOrderData) {
                    $saleOrderData->update([
                        // 'so_id' => $saleOrder->id ?? 0,
                        'product_id' => $requestData['product_id'][$key] ?? $saleOrderData->product_id,
                        'flavour_id' => $requestData['flavour_id'][$key] ?? $saleOrderData->flavour_id,
                        'sale_type' => $requestData['sale_type'][$key] ?? $saleOrderData->sale_type,
                        'rate' => $requestData['rate'][$key] ?? $saleOrderData->rate,
                        'qty' => $requestData['qty'][$key] ?? $saleOrderData->qty,
                        'discount' => $requestData['data_discount'][$key] ?? $saleOrderData->discount,
                        'discount_amount' => $requestData['data_discount_amount'][$key] ?? $saleOrderData->discount_amount,
                        'tax_percent' => $requestData['data_tax_percent'][$key] ?? $saleOrderData->tax_percent,
                        'tax_amount' => $requestData['data_tax_amount'][$key] ?? $saleOrderData->tax_amount,
                        'trade_offer_amount' => $requestData['trade_offer_amount'][$key] ?? 0,
                        'scheme_id' => $scheme_id ?? 0,
                        'scheme_data_id' => $scheme_data_id ?? 0,
                        'scheme_amount' => $requestData['scheme_amount'][$key] ?? 0,
                        'total' => $requestData['data_total'][$key] ?? $saleOrderData->total,
                        'sheme_product_id' => $requestData['shceme_product_id'][$key],
                        'offer_qty' => $requestData['offer'][$key],
                    ]);
                } else {
                    // dd('in',$key);
                    SaleOrderData::create([
                        'so_id' => $id ?? 0,
                        'product_id' => $requestData['product_id'][$key] ?? 0,
                        'flavour_id' => $requestData['flavour_id'][$key] ?? 0,
                        'sale_type' => $requestData['sale_type'][$key] ?? 0,
                        'rate' => $requestData['rate'][$key] ?? 0,
                        'qty' => $requestData['qty'][$key] ?? 0,
                        'discount' => $requestData['data_discount'][$key] ?? 0,
                        'discount_amount' => $requestData['data_discount_amount'][$key] ?? 0,
                        'tax_percent' => $requestData['data_tax_percent'][$key] ?? 0,
                        'tax_amount' => $requestData['data_tax_amount'][$key] ?? 0,
                        'total' => $requestData['data_total'][$key] ?? 0,
                        'sheme_product_id' => $requestData['shceme_product_id'][$key],
                        'offer_qty' => $requestData['offer'][$key],
                    ]);
                }

            }
            DB::commit();
            return response()->json(['success'=>'Sale Order Updated Successfully']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error'=>'Oops! There might be a error '. $th->getMessage()]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     $saleOrderData = SaleOrderData::where('id', $id)->update(['status'=> 0]);
    //     $saleOrder = SaleOrder::where('id', $id)->update(['status'=> 0]);
    //     // return redirect()->back()->with('success', 'Sale Order Deleted Successfully');
    //     return response()->json(['success'=>'Sale Order Deleted Successfully']);
    // }

    public function destroy($id)
{
    try {
         $saleOrderData = SaleOrderData::where('id', $id)->update(['status'=> 0]);
     $saleOrder = SaleOrder::where('id', $id)->update(['status'=> 0]);
        
        return response()->json([
            'success' => 'Sale Order Deleted Successfully'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error deleting record: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Add Product Row on Table
     */
    public function productTableRow($product_id)
    {
        // dd($product_id);
        $product = Product::where('id', $product_id)->first();
        return view($this->page.'ajax.TableProductData', compact('product'));
    }
    /**
     * Get Tso by Distributor ID
     */
    public function getTsoByDistributor(Request $request)
    {
        // dd($request);
        // $id = $request->id;
        // $data = TSO::status()->where('distributor_id', $id)->get();
        // dd($data, $id, $request);
        $distributor = $request->id;
        $tso = $this->master->get_all_tso_by_distributor_id($distributor);
        return  response()->json(['status' => true,'res'=>$tso]);

        // $response = [
        //     'status' => true,
        //     'res' => $data
        // ];
        // return response($response);
    }
    /**
     * Get Shop by TSO ID
     */
    public function getShopByTso(Request $request)
    {

        $id = $request->id;
      $data =  Shop::status()->with('ShopOutstanding:so_amount,rv_amount,sr_amount,shop_id')->where('tso_id',$id)->select('id','company_name','balance_amount','debit_credit')->get();
        // dd($data->toArray());
        $response = [
            'status' => true,
            'res' => $data
        ];
        return response($response);
    }

}
