<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\SaleOrder;
use App\Models\SaleOrderData;
use App\Models\User;
use App\Models\Shop;
use App\Helpers\MasterFormsHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendSmsJob;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\SaleOrderReturn;
use Illuminate\Support\Str;



class SalesController extends BaseController
{
    public function orderCreate(Request $request)
    {
        date_default_timezone_set("Asia/Karachi");
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'notes' => 'required',
            'discount_percent' => 'required',
            'payment_type' => 'required',
            'total_pcs' => 'required',
            'discount_amount' => 'required',
            'total_amount' => 'required',
            'products_subtotal' => 'required',
            'user_id' => 'required|exists:users,id',

            'product_id' => 'required|array',
            'product_id.*' => 'required|gt:0|exists:products,id',  // Validate each item

            'flavour_id' => 'required|array',
            'flavour_id.*' => 'required|gt:0|exists:product_flavours,id',

            'sale_type' => 'required|array',
            'sale_type.*' => 'required|gt:0',

            'rate' => 'required|array',
            'rate.*' => 'required|gt:0',

            'qty' => 'required|array',
            'qty.*' => 'required|gt:0',

            'discount' => 'required|array',
            'discount_amount_data' => 'required|array',
            'total' => 'required|array',
            'total.*' => 'required|gt:0',

            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        DB::beginTransaction();
        try {

            $shop_data=  Shop::where('id',$request->shop_id)->first();
            $distributor_id = $shop_data->distributor_id;

            $request['invoice_no'] = SaleOrder::UniqueNo();
            $tso = User::find($request->user_id)->tso;
            $request['cost_center'] = 0;
            $request['transport_details'] = 0;
            $request['excecution'] =$request->type;
            $request['tso_id'] = $tso->id;
            $request['distributor_id'] = $distributor_id;
            $request['dc_date'] = date('Y-m-d');
            $request['delivery_date'] =  $request->delivery_date ?? '0000-00-00';
            // dd($request->all());
            $signature ='';
            if ($request->file('signature_image')) {
                $file = $request->file('signature_image');
                $signature = time() . '-' . $file->getClientOriginalName();
                $file->storeAs('sales', $signature, 'public'); // 'uploads' is the directory to store files.
            }

            if(!empty($signature))
                {
                   $signature = $signature;
                }

                $marchadising ='';
                if ($request->file('merchandising_image')) {
                    $file = $request->file('merchandising_image');
                    $marchadising =  time() . '-' .$file->getClientOriginalName();
                    $file->storeAs('sales', $marchadising, 'public'); // 'uploads' is the directory to store files.
                }

                if(!empty($marchadising))
                    {
                       $marchadising = $marchadising;
                    }
               $data = $request->only('user_id','tso_id','dc_date','delivery_date','distributor_id','transport_details','cost_center','invoice_no','shop_id','notes','discount_percent','payment_type','total_pcs','discount_amount','total_amount','products_subtotal','excecution');
               $data['signature_image'] = $signature;
               $data['merchandising_image'] = $marchadising;

            $saleOrder = SaleOrder::create($data);
            MasterFormsHelper::users_location_submit($saleOrder,$request->latitude,$request->longitude,'sale_orders', 'Create Sale Order');
            // dd($saleOrder);
            $request['discount_amount'] = $request->discount_amount_data;
            $saleOrder->products_subtotal = 1000;
            $saleOrder->save();

            $total_amount = 0;
            $total_qty = 0;
            // dd($request->all() ,$saleOrder , $saleOrder->total_amount , $saleOrder->products_subtotal);
            foreach ($request->product_id as $key => $product_id) {
                $total = ($request->rate[$key] * $request->qty[$key]);
                $scheme_amount = $request->sheme_amount[$key] ?? 0;
                $trade_offer_amount = $request->trade_offer_amount[$key] ?? 0;
                $discount_amount = isset($request->discount[$key]) && ($request->discount[$key]!=0) ? (( $total / 100 ) * $request->discount[$key]) : 0;
                $total = $total -$discount_amount - $scheme_amount - $trade_offer_amount;
                $saleOrder->saleOrderData()->create([
                    'product_id' => $request->product_id[$key],
                    'flavour_id' => $request->flavour_id[$key],
                    'sale_type' => $request->sale_type[$key],
                    'rate' => $request->rate[$key],
                    'qty' => $request->qty[$key],
                    'foc' => $request->foc[$key] ?? 0,
                    'availability' => $request->availability[$key] ?? 0,
                    'discount' => $request->discount[$key] ?? 0,
                    'discount_amount' => $discount_amount,
                    'total' => $total,
                    'sheme_product_id' => $request->shceme_product_id[$key] ?? 0,
                    'offer_qty' => $request->offer[$key] ?? 0,
                    'scheme_id' => $request->scheme_id[$key] ?? 0,
                    'scheme_data_id' => $request->scheme_data_id[$key] ?? 0,
                    'scheme_amount' => $scheme_amount,
                    'trade_offer_amount' => $trade_offer_amount,

                ]);
                $total_amount+= $total;
                $total_qty += $request->qty[$key];
            }
            $total_amount = $request->total_amount??$total_amount;
            SaleOrder::find($saleOrder->id)->update(['total_amount'=>$total_amount , 'total_pcs'=>$total_qty]);

            // $shop_data['mobile_no'] = MasterFormsHelper::correctPhoneNumber($shop_data['mobile_no']);
            // $text = "New Order has been Booked";
            // $text .= "\nShop Name : $shop_data->company_name";
            // $text .= "\nContact Person : $shop_data->contact_person";
            // $text .= "\n Invoice No : $saleOrder->invoice_no";
            // SendSmsJob::dispatch( $shop_data['mobile_no'] , $text);

            DB::commit();
            return $this->sendResponse([], 'Sale Order Create Successfully.');
        } catch (Exception $th) {
            DB::rollBack();
            return $this->sendError("Server Error!",['error'=> $th->getMessage() . ' ' . $th->getLine()]);
        }
    }



public function ReturnSaleOrderList(Request $request)
{
    $sales_return = SaleOrderReturn::with([
        'returnDetails.product:id,product_name',
        'distributor:id,distributor_name',
        'tso:id,name',
        'shop:id,company_name,status'
    ])
    ->where('status', 1) // ✅ active returns only
    ->whereHas('shop', function ($q) {
        $q->where('status', 1); // ✅ active shops only
    })
    ->when($request->distributor_id, function ($query) use ($request) {
        $query->where('distributor_id', $request->distributor_id);
    })
    ->when($request->tso_id, function ($query) use ($request) {
        $query->where('tso_id', $request->tso_id);
    })
    ->when($request->from && $request->to, function ($query) use ($request) {
        $query->whereBetween('return_date', [$request->from, $request->to]);
    })
    ->get()
    ->map(function ($row) {
        return [
            'id' => $row->id,
            'return_no' => strtoupper($row->return_no),
            'distributor_name' => strtoupper($row->distributor->distributor_name ?? 'N/A'),
            'tso_name' => strtoupper($row->tso->name ?? 'N/A'),
            'shop_name' => strtoupper($row->shop->company_name ?? 'N/A'),
            'execution_status' => $row->excecution ? 'Yes' : 'No',
            'details' => $row->returnDetails->map(function ($detail) {
                return [
                    'product_name' => $detail->product->product_name ?? 'N/A',
                    'qty' => $detail->quantity ?? 0,
                    'reason' => $detail->reason ?? '',
                    'remarks' => $detail->remarks ?? '',
                ];
            }),
        ];
    });

    return response()->json([
        'status' => true,
        'data' => $sales_return
    ]);
}

    // $sales_return =  SaleOrderReturn::with('returnDetails', 'distributor', 'tso', 'shop')
    //     ->when($request->distributor_id, function ($query) use ($request) {
    //         $query->where('distributor_id', $request->distributor_id);
    //     })
    //     ->when($request->tso_id, function ($query) use ($request) {
    //         $query->whereHas('tso', function ($q) use ($request) {
    //             $q->where('id', $request->tso_id);
    //         });
    //     })
    //     ->when($request->from && $request->to, function ($query) use ($request) {
    //         $query->whereBetween('return_date', [$request->from, $request->to]);
    //     })
    //     ->get();

    // $data = $sales_return->map(function ($row, $index) {
    //     return [
    //         'id' => $row->id,
    //         'sr_no' => $index + 1,
    //         'return_no' => strtoupper($row->return_no),
    //         'distributor' => strtoupper($row->distributor->distributor_name ?? 'N/A'),
    //         'tso' => strtoupper($row->tso->name ?? 'N/A'),
    //         'shop' => strtoupper($row->shop->company_name ?? 'N/A'),
    //         'excecution' => $row->excecution ? 'Yes' : 'No',
    //     ];
    // });

    // return response()->json([
    //     'status' => true,
    //     'message' => 'Sales Return List',
    //     'data' => $data,
    // ]);




 
//  public function orderCreateNew(Request $request)
//     {

//         // dd($request->all());
//         date_default_timezone_set("Asia/Karachi");
//         $request->validate([
//             'shop_id' => 'required|exists:shops,id',
//             'notes' => 'required',
//             'discount_percent' => 'required',
//             'payment_type' => 'required',
//             'total_pcs' => 'required',
//             'discount_amount' => 'required',
//             'total_amount' => 'required',
//             'products_subtotal' => 'required',
//             'user_id' => 'required|exists:users,id',

//             'product_id' => 'required|array',
//             'product_id.*' => 'required|gt:0|exists:products,id',  // Validate each item

//             'flavour_id' => 'required|array',
//             'flavour_id.*' => 'required|gt:0|exists:product_flavours,id',

//             'sale_type' => 'required|array',
//             'sale_type.*' => 'required|gt:0',

//             'rate' => 'required|array',
//             'rate.*' => 'required|gt:0',

//             'qty' => 'required|array',
//             'qty.*' => 'required|gt:0',

//             'discount' => 'required|array',
//             'discount_amount_data' => 'required|array',
//             'total' => 'required|array',
//             'total.*' => 'required|gt:0',

//             'latitude' => 'required',
//             'longitude' => 'required',
//         ]);

//         // dd($request->all());
//         DB::beginTransaction();
//         try {

//             $shop_data=  Shop::where('id',$request->shop_id)->first();
//             $distributor_id = $shop_data->distributor_id;

//             $request['invoice_no'] = SaleOrder::UniqueNo();
//             $tso = User::find($request->user_id)->tso;
//             $request['cost_center'] = 0;
//             $request['transport_details'] = 0;
//             $request['excecution'] =$request->type;
//             $request['tso_id'] = $tso->id;
//             $request['distributor_id'] = $distributor_id;
//             $request['dc_date'] = date('Y-m-d');
//             $request['delivery_date'] =  $request->delivery_date ?? '0000-00-00';
//             // dd($request->all());
//             $signature ='';
//             if ($request->file('signature_image')) {
//                 $file = $request->file('signature_image');
//                 $signature = time() . '-' . $file->getClientOriginalName();
//                 $file->storeAs('sales', $signature, 'public'); // 'uploads' is the directory to store files.
//             }

//             if(!empty($signature))
//                 {
//                    $signature = $signature;
//                 }

//                 $marchadising ='';
//                 if ($request->file('merchandising_image')) {
//                     $file = $request->file('merchandising_image');
//                     $marchadising =  time() . '-' .$file->getClientOriginalName();
//                     $file->storeAs('sales', $marchadising, 'public'); // 'uploads' is the directory to store files.
//                 }

//                 if(!empty($marchadising))
//                     {
//                        $marchadising = $marchadising;
//                     }
//                $data = $request->only('user_id','tso_id','dc_date','delivery_date','distributor_id','transport_details','cost_center','invoice_no','shop_id','notes','discount_percent','payment_type','total_pcs','discount_amount','total_amount','products_subtotal','excecution');
//                $data['signature_image'] = $signature;
//                $data['merchandising_image'] = $marchadising;
         
//             $saleOrder = SaleOrder::create($data);
//             MasterFormsHelper::users_location_submit($saleOrder,$request->latitude,$request->longitude,'sale_orders', 'Create Sale Order');
//             // dd($saleOrder);
//             $request['discount_amount'] = $request->discount_amount_data;
//             $saleOrder->products_subtotal = 1000;
//             $saleOrder->save();

//             $total_amount = 0;
//             $total_qty = 0;
//             // dd($request->all() ,$saleOrder , $saleOrder->total_amount , $saleOrder->products_subtotal);

          
//             foreach ($request->product_id as $key => $product_id) {
              



//                 $scheme_id = 0;
//                 $scheme_data_id = 0;
//                 $scheme_id_pcs = 0;
//                 $scheme_data_id_pcs = 0;
//                 $scheme_amount = 0; // Ensure it's defined
//                 $scheme_amount1 = 0; // Ensure it's defined
                
//                 if (!empty($request->type_scheme[$key])) { 
//                     if ($request->type_scheme[$key] === 'amount') {
//                         $scheme_id = $request->scheme_id[$key] ?? 0;
//                         $scheme_data_id = $request->scheme_data_id[$key] ?? 0;
//                         $scheme_amount = $request->scheme_value[$key] ?? 0;
//                     } elseif ($request->type_scheme[$key] === 'pcs') {
//                         $scheme_id_pcs = $request->scheme_id[$key] ?? 0;
//                         $scheme_data_id_pcs = $request->scheme_data_id[$key] ?? 0;
//                         $scheme_amount1 = $request->scheme_value[$key] ?? 0;
//                     }
//                 }
                

//                 $total = ($request->rate[$key] * $request->qty[$key]);
//                 $scheme_amount = $scheme_amount ?? 0;
//                 $trade_offer_amount = $request->trade_offer_amount[$key] ?? 0;
//                 $discount_amount = isset($request->discount[$key]) && ($request->discount[$key]!=0) ? (( $total / 100 ) * $request->discount[$key]) : 0;
//                 $total = $total -$discount_amount - $scheme_amount - $trade_offer_amount;

//                 $saleOrder->saleOrderData()->create([
//                     'product_id' => $request->product_id[$key],
//                     'flavour_id' => $request->flavour_id[$key],
//                     'sale_type' => $request->sale_type[$key],
//                     'rate' => $request->rate[$key],
//                     'qty' => $request->qty[$key],
//                     'foc' => $request->foc[$key] ?? 0,
//                     'availability' => $request->availability[$key] ?? 0,
//                     'discount' => $request->discount[$key] ?? 0,
//                     'discount_amount' => $discount_amount,
//                     'total' => $total,
//                     'sheme_product_id' => $request->shceme_product_id[$key] ?? 0,
//                     'offer_qty' => $request->offer[$key] ?? 0,
//                     'scheme_id' => $scheme_id ?? 0,
//                     'scheme_data_id' => $scheme_data_id ?? 0,
//                     'scheme_amount' => $scheme_amount ?? 0,

//                     'scheme_id_pcs' => $scheme_id_pcs ?? 0,
//                     'scheme_data_id_pcs' => $scheme_data_id_pcs ?? 0,
//                     'scheme_data_pcs' => $scheme_amount1 ?? 0,
//                     'trade_offer_amount' => $trade_offer_amount,

//                 ]);
//                 $total_amount+= $total;
//                 $total_qty += $request->qty[$key];
//             }
//             $total_amount = $request->total_amount??$total_amount;
//             SaleOrder::find($saleOrder->id)->update(['total_amount'=>$total_amount , 'total_pcs'=>$total_qty]);

//             // $shop_data['mobile_no'] = MasterFormsHelper::correctPhoneNumber($shop_data['mobile_no']);
//             // $text = "New Order has been Booked";
//             // $text .= "\nShop Name : $shop_data->company_name";
//             // $text .= "\nContact Person : $shop_data->contact_person";
//             // $text .= "\n Invoice No : $saleOrder->invoice_no";
//             // SendSmsJob::dispatch( $shop_data['mobile_no'] , $text);

//             DB::commit();
//             return $this->sendResponse([], 'Sale Order Create Successfully.');
//         } catch (Exception $th) {
//             DB::rollBack();
//             return $this->sendError("Server Error!",['error'=> $th->getMessage() . ' ' . $th->getLine()]);
//         }
//     }




public function CreateReturnSaleOrder(Request $request)
{
   


    $rules = [
        'distributor_id' => 'required|integer',
        'tso_id' => 'required|integer',
        'shop_id' => 'required|integer',
        'details' => 'required|array|min:1',
        'details.*.product_id' => 'required|integer',
        'details.*.qty' => 'required|integer|min:1',
        'details.*.reason' => 'nullable|string',
        'details.*.remarks' => 'nullable|string',
        'details.*.damage_photo' => 'nullable|file|image|mimes:jpg,jpeg,png|max:2048',
       
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        DB::beginTransaction();

        $orderId = DB::table('sale_order_returns')->insertGetId([
            'user_id' => Auth::id(),
          
            'distributor_id' => $request->distributor_id,
            'tso_id' => $request->tso_id,
            'shop_id' => $request->shop_id,
            'return_date' => now()->toDateString(),
           
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $return_no = 'SR-' . str_pad($orderId, 4, '0', STR_PAD_LEFT);

        DB::table('sale_order_returns')->where('id', $orderId)->update([
            'return_no' => $return_no
        ]);
      

     

        foreach ($request->details as $index => $detail) {
            $damagePhotoPath = null;

            if ($request->hasFile("details.$index.damage_photo")) {
                $photoFile = $request->file("details.$index.damage_photo");
                $damagePhotoPath = $photoFile->store('damage_photos', 'public');
            }

            DB::table('sale_order_return_details')->insert([
                'sale_order_return_id' => $orderId,
                'product_id' => $detail['product_id'],
                'quantity' => $detail['qty'],
                'reason' => $detail['reason'] ?? null,
                'damage_photo' => $damagePhotoPath,
             
                'remarks' => $detail['remarks'] ?? null,
                 'user_id' => Auth::id(),
          
                    'distributor_id' => $request->distributor_id,
                    'tso_id' => $request->tso_id,
                    'shop_id' => $request->shop_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::commit();

        return response()->json([
            'message' => 'Sale return created successfully!',
            'success' => true
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Failed to create sale return.',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function orderCreateNew(Request $request)
{
    date_default_timezone_set("Asia/Karachi");

    // ✅ Custom validation block
    $validator = Validator::make($request->all(), [
        'shop_id' => 'required|exists:shops,id',
        'notes' => 'required',
        'discount_percent' => 'required',
        'payment_type' => 'required',
        'total_pcs' => 'required',
        'discount_amount' => 'required',
        'total_amount' => 'required',
        'products_subtotal' => 'required',
        'user_id' => 'required|exists:users,id',

        'product_id' => 'required|array',
        'product_id.*' => 'required|gt:0|exists:products,id',

        'flavour_id' => 'required|array',
        'flavour_id.*' => 'required|gt:0|exists:product_flavours,id',

        'sale_type' => 'required|array',
        'sale_type.*' => 'required|gt:0',

        'rate' => 'required|array',
        'rate.*' => 'required|gt:0',

        'qty' => 'required|array',
        'qty.*' => 'required|gt:0',

        'discount' => 'required|array',
        'discount_amount_data' => 'required|array',
        'total' => 'required|array',
        'total.*' => 'required|gt:0', 


    ]);

    if ($validator->fails()) {
        // Convert all errors to a single string message
        $allErrors = implode(' | ', collect($validator->errors()->all())->toArray());

        return response()->json([
            'status' => false,
            'message' => $allErrors,  // All validation errors shown here
        ], 422);
    }

    DB::beginTransaction();
    try {
        $shop_data = Shop::where('id', $request->shop_id)->first();
        $distributor_id = $shop_data->distributor_id;

        $request['invoice_no'] = SaleOrder::UniqueNo();
        $tso = User::find($request->user_id)->tso;
        $request['cost_center'] = 0;
        $request['transport_details'] = 0;
        $request['excecution'] = $request->type;
        $request['tso_id'] = $tso->id;
        $request['distributor_id'] = $distributor_id;
        $request['dc_date'] = date('Y-m-d');
        $request['delivery_date'] = $request->delivery_date ?? '0000-00-00';

        // Handle signature image
        $signature = '';
        if ($request->file('signature_image')) {
            $file = $request->file('signature_image');
            $signature = time() . '-' . $file->getClientOriginalName();
            $file->storeAs('sales', $signature, 'public');
        }

        // Handle merchandising image
        $marchadising = '';
        if ($request->file('merchandising_image')) {
            $file = $request->file('merchandising_image');
            $marchadising = time() . '-' . $file->getClientOriginalName();
            $file->storeAs('sales', $marchadising, 'public');
        }



        $data = $request->only(
            'user_id', 'tso_id', 'dc_date', 'delivery_date',
            'distributor_id', 'transport_details', 'cost_center',
            'invoice_no', 'shop_id', 'notes', 'discount_percent',
            'payment_type', 'total_pcs', 'discount_amount',
            'total_amount', 'products_subtotal', 'excecution'
        );
        $data['signature_image'] = $signature;
        $data['merchandising_image'] = $marchadising;

if ($request->has('order_time') && !empty($request->order_time)) {
    $data['created_at'] = $request->order_time;
}


        $saleOrder = SaleOrder::create($data);
      MasterFormsHelper::users_location_submit($saleOrder, $request->latitude, $request->longitude, 'sale_orders', 'Create Sale Order');

        $request['discount_amount'] = $request->discount_amount_data;
        $saleOrder->products_subtotal = 1000;
        $saleOrder->save();

        $total_amount = 0;
        $total_qty = 0;

        foreach ($request->product_id as $key => $product_id) {
            $scheme_id = 0;
            $scheme_data_id = 0;
            $scheme_id_pcs = 0;
            $scheme_data_id_pcs = 0;
            $scheme_amount = 0;
            $scheme_amount1 = 0;

            if (!empty($request->type_scheme[$key])) {
                if ($request->type_scheme[$key] === 'amount') {
                    $scheme_id = $request->scheme_id[$key] ?? 0;
                    $scheme_data_id = $request->scheme_data_id[$key] ?? 0;
                    $scheme_amount = $request->scheme_value[$key] ?? 0;
                } elseif ($request->type_scheme[$key] === 'pcs') {
                    $scheme_id_pcs = $request->scheme_id[$key] ?? 0;
                    $scheme_data_id_pcs = $request->scheme_data_id[$key] ?? 0;
                    $scheme_amount1 = $request->scheme_value[$key] ?? 0;
                }
            }

            $total = ($request->rate[$key] * $request->qty[$key]);
            $trade_offer_amount = $request->trade_offer_amount[$key] ?? 0;
            $discount_amount = isset($request->discount[$key]) && ($request->discount[$key] != 0) ? (($total / 100) * $request->discount[$key]) : 0;
            $total = $total - $discount_amount - $scheme_amount - $trade_offer_amount;

            $saleOrder->saleOrderData()->create([
                'product_id' => $request->product_id[$key],
                'flavour_id' => $request->flavour_id[$key],
                'sale_type' => $request->sale_type[$key],
                'rate' => $request->rate[$key],
                'qty' => $request->qty[$key],
                'foc' => $request->foc[$key] ?? 0,
                'availability' => $request->availability[$key] ?? 0,
                'discount' => $request->discount[$key] ?? 0,
                'discount_amount' => $discount_amount,
                'total' => $total,
                'sheme_product_id' => $request->shceme_product_id[$key] ?? 0,
                'offer_qty' => $request->offer[$key] ?? 0,
                'scheme_id' => $scheme_id,
                'scheme_data_id' => $scheme_data_id,
                'scheme_amount' => $scheme_amount,
                'scheme_id_pcs' => $scheme_id_pcs,
                'scheme_data_id_pcs' => $scheme_data_id_pcs,
                'scheme_data_pcs' => $scheme_amount1,
                'trade_offer_amount' => $trade_offer_amount,
            ]);

            $total_amount += $total;
            $total_qty += $request->qty[$key];
        }

        $total_amount = $request->total_amount ?? $total_amount;
        SaleOrder::find($saleOrder->id)->update([
            'total_amount' => $total_amount,
            'total_pcs' => $total_qty
        ]);

        DB::commit();
        return $this->sendResponse([], 'Sale Order Created Successfully.');
    } catch (Exception $th) {
        DB::rollBack();
        return $this->sendError("Server Error!", ['error' => $th->getMessage() . ' on line ' . $th->getLine()]);
    }
}
  public function orderUpdate(Request $request)
    {


        $request->validate([
            'id' => 'required',
            'product_id' => 'required|array|exists:products,id',
            'flavour_id' => 'required|array|exists:product_flavours,id',

        ]);
        DB::beginTransaction();
        try {

     $sale_oder =  SaleOrder::find($request->id);
     $sale_oder->shop_id = $request->shop_id;
     if($sale_oder->excecution ==  0):

               $sale_oder->save();
            //    $request['discount_amount'] = $request->discount_amount_data;

               SaleOrderData::where('so_id',$request->id)->delete();
                $total_amount = 0;
                $total_qty = 0;
                foreach ($request->product_id as $key => $product_id):


                    $scheme_id = 0;
                    $scheme_data_id = 0;
                    $scheme_id_pcs = 0;
                    $scheme_data_id_pcs = 0;
                    $scheme_amount = 0; // Ensure it's defined
                    $scheme_amount1 = 0; // Ensure it's defined
                    
                    if (!empty($request->type_scheme[$key])) { 
                        if ($request->type_scheme[$key] === 'amount') {
                            $scheme_id = $request->scheme_id[$key] ?? 0;
                            $scheme_data_id = $request->scheme_data_id[$key] ?? 0;
                            $scheme_amount = $request->scheme_value[$key] ?? 0;
                        } elseif ($request->type_scheme[$key] === 'pcs') {
                            $scheme_id_pcs = $request->scheme_id[$key] ?? 0;
                            $scheme_data_id_pcs = $request->scheme_data_id[$key] ?? 0;
                            $scheme_amount1 = $request->scheme_value[$key] ?? 0;
                        }
                    }

                    
                    $total = ($request->rate[$key] * $request->qty[$key]);
                    //$scheme_amount = $request->sheme_amount[$key] ?? 0;
                    $trade_offer_amount = $request->trade_offer_amount[$key] ?? 0;
                    $discount_amount = ($request->discount[$key]!=0) ? (( $total / 100 ) * $request->discount[$key]) : 0;
                    $total = $total -$discount_amount - $scheme_amount - $trade_offer_amount;
                    $sale_oder->saleOrderData()->create([
                        'product_id' => $request->product_id[$key],
                        'flavour_id' => $request->flavour_id[$key],
                        'sale_type' => $request->sale_type[$key],
                        'rate' => $request->rate[$key],
                        'qty' => $request->qty[$key],
                        'foc' => $request->foc[$key] ?? 0,
                        'availability' => $request->availability[$key] ?? 0,
                        'discount' => $request->discount[$key],
                        'discount_amount' => $discount_amount,
                        'total' => $total,
                        'sheme_product_id' => $request->shceme_product_id[$key] ?? 0,
                        'offer_qty' => $request->offer[$key] ?? 0,
                        'scheme_id' => $scheme_id ?? 0,
                        'scheme_data_id' => $scheme_data_id ?? 0,
                        'scheme_amount' => $scheme_amount ?? 0,

                        'scheme_id_pcs' => $scheme_id_pcs ?? 0,
                        'scheme_data_id_pcs' => $scheme_data_id_pcs ?? 0,
                        'scheme_data_pcs' => $scheme_amount1 ?? 0,
                        'trade_offer_amount' => $trade_offer_amount,
                    ]);
                    $total_amount+= $total;
                    $total_qty += $request->qty[$key];
                endforeach;
                $total_amount = $request->total_amount??$total_amount;
                SaleOrder::find($request->id)->update(['total_amount'=>$total_amount, 'discount_percent' => $request->discount_percent, 'discount_amount' => $request->discount_amount, 'total_pcs'=>$total_qty]);
            else:

            return $this->sendError('Sale excecution.', ['error'=>'Can not update because Sale is excecuted ']);
            endif;


            DB::commit();
            return $this->sendResponse([], 'Sale Order Update Successfully.');

        } catch (Exception $th) {
            DB::rollBack();
            return $this->sendError("Server Error!",['error'=> $th->getMessage()]);
        }


    }


    public function orderList(Request $request)
    {
        $sale_oder = SaleOrder::with('shop:id,title,company_name')
        ->where('user_id',$request->user_id);
        // if(!empty($request->type)){
            $sale_oder->where('excecution',$request->type);
        // }
        $sale_oder =  $sale_oder->latest()
        ->paginate($request->limit??50);
        return $this->sendResponse($sale_oder,'SaleOder List Successfully Retrive');
    }
    public function OrderDetails(Request $request)
    {
        if($request->id)
        {
         $id = $request->id;
        $sale_oder = SaleOrder::with('saleOrderData','shop:id,title,company_name')->find($id);
        return $this->sendResponse( $sale_oder,'SaleOder Data');
        }

    }
}
