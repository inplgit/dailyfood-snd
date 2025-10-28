<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\MasterFormsHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImportStock;
use App\Http\Requests\StoreStockManagementRequest;
use App\Models\Distributor;
use App\Models\Product;
use App\Models\Stock;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

// use Maatwebsite\Excel\Excel;

class StockManagementController extends Controller
{

    protected $page;

    public function __construct()
    {
        $this->page = "pages.Distributor.StockManagement.";
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $distributors = MasterFormsHelper::get_all_distributor_user_wise();
        if ($request->ajax()):
            $stocks = Distributor::find($request->distributor_id)->stocks()
            ->whereHas('product', function ($query) {
                $query->where('status', 1); // Condition on Product model where status = 1
            })
            ->select('product_id','distributor_id' , 'flavour_id' , 'uom_id')
            ->where('status' , 1)
            ->groupBy('product_id' , 'flavour_id')
            ->get();
            return  view($this->page.'stockListAjax',compact('stocks'));
         endif;
        return view($this->page. 'stockList',compact('distributors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view($this->page . "addStockForm");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStockManagementRequest $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            foreach ($request->product_id as $key => $product_id) {
                Stock::create([
                    "distributor_id"      => $request->distributor_id,
                    "voucher_no"      => '',
                    "stock_received_type" => $request->stock_received_type,
                    "voucher_date"        => $request->voucher_date,
                    "stock_type"          => $request->stock_type,
                    "remarks"             => $request->remarks,
                    "product_id"          => $request->product_id[$key],
                    "flavour_id"          => $request->flavour_id[$key],
                    "uom_id"              => $request->uom_id[$key],
                    "qty"                 => $request->qty[$key],
                ]);
            }
            DB::commit();
            return response()->json(['success' => 'Stock Add Successfully']);
        } catch (Exception $th) {
            //throw $th;
            DB::rollback();
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
        //
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
        //
    }

    public function importStock()
    {
        return view($this->page."importStockForm");
    }

    public function uploadStockFIle(Request $request)
    {
        $request->validate([
            'voucher_date' => 'required',
            'stock_type' => 'required',
            'file' => 'required|max:10000|mimes:csv,xlsx,xls'
        ]);
        DB::beginTransaction();
        try {
            $file = Excel::toArray([], $request->file('file'));
            $file = $file[0];
            $distributorNotExist = [];
            $productNotExist = [];
            if($file[0][0] == trim("Distributor Code") && $file[0][1] == trim("Distributor (Company Name)") && $file[0][2] == trim("Product Code (SKU)") && $file[0][3] == trim("Product Description (Name)") && $file[0][4] == trim("Quantity") && $file[0][5] == trim("Remarks")){
                foreach ($file as $key => $value) {
                    // dd($key , $file , $value);
                    if($key == 0) continue ;
                    $distributor_id = Distributor::where('distributor_code',trim($value[0]))->where('distributor_name',trim($value[1]))->first();
                    if(!$distributor_id) array_push($distributorNotExist,$value[0]);
                    $product_id = Product::where('SKU',trim($value[2]))->where('product_name',trim($value[3]))->first();
                    if(!$product_id) array_push($productNotExist,$value[2]);
                    if(!$distributor_id || !$product_id) continue;
                    // dd($distributor_id , $product_id);
                    $data = [
                        "distributor_id"      => $distributor_id->id,
                        "stock_received_type" => 1,
                        "voucher_date"        => $request->voucher_date,
                        "stock_type"          => $request->stock_type,
                        "remarks"             => trim($value[5]),
                        "product_id"          => $product_id->id,
                        "qty"                 => trim($value[4]),
                    ];
                    Stock::create($data);
                }
                // if (!$productNotExist || !$distributorNotExist) {
                // }
                // dd();
                DB::commit();
                if($productNotExist){Session::flash('proNotExist',$productNotExist);}
                if($distributorNotExist){ Session::flash('distriNotExist',$distributorNotExist);}
                return redirect()->back();
            }else{
                return redirect()->back()->with('catchError',"Format Not Match");
            }
        } catch (Exception $th) {
            //throw $th;
            DB::rollback();
            return response()->json(['catchError' => $th->getMessage()]);
        }

    }

    public function import_stock_list(Request $request)
    {
        // dd('adsa');
        $distributors = MasterFormsHelper::get_all_distributor_user_wise();
        if ($request->ajax()):
            $stocks = Distributor::find($request->distributor_id)->stocks()->where('stock_type' , 0)->where('status' , 1)->select('voucher_date','product_id','distributor_id' , 'flavour_id' , 'uom_id' , 'qty')->get();
            // dd($stocks->toArray());
            return  view($this->page.'import_stock_list_ajax',compact('stocks'));
         endif;
        return view($this->page. 'import_stock_list',compact('distributors'));
    }
}
