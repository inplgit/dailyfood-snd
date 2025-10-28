<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\Stock;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;

class OpeningController extends Controller
{

    public function __construct()
    {
        $this->page = "pages.Distributor.Opening.";
    }

    public function add_opening_form(Request $request)
    {

  
   
        $stock = [];
        if ($request->id!=''):
            $stock = Product::leftJoinSub(function ($query) use ($request) {
                    $query->select('product_id', DB::raw('sum(qty) as qty'), 'voucher_date' , 'distributor_id')
                    ->from('stocks')
                    ->where('distributor_id', $request->id)
                    ->where('stock_type', 1)
                    ->groupBy('product_id');
            }, 'stock_sub', 'products.id', '=', 'stock_sub.product_id')
                ->where('products.status', 1)
                // ->where('products.product_type_id', 1)
                ->select('products.product_name', 'products.id', DB::raw('COALESCE(stock_sub.qty, 0) as qty'),DB::raw('COALESCE(stock_sub.voucher_date, 0) as voucher_date')
                ,DB::raw('COALESCE(stock_sub.distributor_id, 0) as distributor_id')
                // ,DB::raw('COALESCE(stock_sub.uom_id, 0) as uom_id'),DB::raw('COALESCE(stock_sub.flavour_id, 0) as flavour_id')
                )
                // ->where('distributor_id', $request->id)
                ->get();

              

                // $stock = Product::where('products.status', 1)->get();
            // dd($stock->toArray());
         endif;


         
        return view ($this->page.'add_opening_form' ,compact('stock'));
    }

//     public function insert_opening(Request $request)
//     {
//         // dd($request->all());
//         DB::beginTransaction();
//         try {
//         $validatedData =  Validator::make($request->all(),[
//             'distributor_id' => 'required',
//             'open_date' => 'required',

//         ]);

//         if ($validatedData->fails()):

//             return response()->json(['error' => $validatedData->errors()]);
//         endif;

//         // Stock::where('stock_type',1)->where('distributor_id',$request->distributor_id)->delete();
//         // foreach($request->product_id as $key => $row):

//         //     if ($request->qty[$key] > 0):
//         //         Stock::create([
//         //             "distributor_id"      => $request->distributor_id,
//         //             "stock_received_type" => 1,
//         //             "voucher_date"        => $request->open_date,
//         //             "stock_type"          => 1,
//         //             "product_id"          => $row,
//         //             "qty"                 => $request->qty[$key],
//         //         ]);
//         //     endif;
//         // endforeach;

//         foreach ($request->qty as $product_id => $flavour) {
//             foreach ($flavour as $flavour_id => $uom) {
//                 foreach ($uom as $uom_id => $qty) {
//                     $stock = Stock::where('stock_type',1)->where('distributor_id',$request->distributor_id)
//                     ->where('product_id' , $product_id)->where('flavour_id' , $flavour_id)->where('uom_id' , $uom_id);
//                     if ($stock->first()) {
//                         if ($qty > 0) {
//                             $stock->update([
//                                 "voucher_date"        => $request->open_date,
//                                 "qty"                 => $qty,
//                             ]);
//                         }
//                         else
//                         {
//                             $stock->delete();
//                         }
//                     }
//                     else
//                     {
//                         if ($qty > 0):
//                             Stock::create([
//                                 "distributor_id"      => $request->distributor_id,
//                                 "stock_received_type" => 1,
//                                 "voucher_date"        => $request->open_date,
//                                 "stock_type"          => 1,
//                                 "product_id"          => $product_id,
//                                 "flavour_id"          => $flavour_id,
//                                 "uom_id"              => $uom_id,
//                                 "qty"                 => $qty,
//                             ]);
//                         endif;
//                     }
//                 }
//             }
//         }

//         DB::commit();
//         return response()->json(['success' => 'Stock Add Successfully','update'=>true]);
//     } catch (Exception $th) {
//         //throw $th;
//         DB::rollback();
//         return response()->json(['catchError' => $th->getMessage()]);
//     }
//   }


public function insert_opening(Request $request)
{
    //  dd($request->all());
    DB::beginTransaction();

    try {
        // Validate request
        $validatedData = Validator::make($request->all(), [
            'distributor_id' => 'required',
            'open_date' => 'required|date',
        ]);

        if ($validatedData->fails()) {
            return response()->json(['error' => $validatedData->errors()]);
        }

        // Process each product's quantities
        foreach ($request->qty as $product_id => $flavour) {
            foreach ($flavour as $flavour_id => $uom) {
                foreach ($uom as $uom_id => $qty) {
                    // Fetch the stock record for the given distributor, product, flavour, and UOM
                    $stock = Stock::where('stock_type', 1)
                        ->where('distributor_id', $request->distributor_id)
                        ->where('product_id', $product_id)
                        ->where('flavour_id', $flavour_id)
                        ->where('uom_id', $uom_id)
                        ->latest('updated_at')
                        ->first();


    //                 $stock = Stock::where('stock_type', 1)
    // ->where('distributor_id', $request->distributor_id)
    // ->where('product_id', $product_id)
    // ->where('flavour_id', $flavour_id)
    // ->where('uom_id', $uom_id)
    // ->first();

                    if ($stock) {
                        if ($qty > 0) {
                            // Update existing stock record
                            $stock->update([
                                "voucher_date" => $request->open_date,
                                "qty" => $qty, // Replace existing qty with the new value
                                "updated_at" => now(),
                            ]);
                        } else {
                            // Delete record if qty is 0 or less
                            // $stock->delete();
                            $stock->update([
                                "voucher_date" => $request->open_date,
                                "qty" => 0, 
                                "updated_at" => now(),
                            ]);
                        }
                    } else {
                        if ($qty > 0) {
                            // Create new stock record
                            Stock::create([
                                "distributor_id" => $request->distributor_id,
                                "stock_received_type" => 1,
                                "voucher_date" => $request->open_date,
                                "stock_type" => 1,
                                "product_id" => $product_id,
                                "flavour_id" => $flavour_id,
                                "uom_id" => $uom_id,
                                "qty" => $qty,
                                "updated_at" => now(),
                            ]);
                        }
                    }
                }
            }
        }

        DB::commit();
        return response()->json(['success' => 'Stock added or updated successfully.', 'update' => true]);

    } catch (Exception $e) {
        // Rollback transaction on error
        DB::rollback();
        return response()->json(['catchError' => $e->getMessage()]);
    }
}

}
