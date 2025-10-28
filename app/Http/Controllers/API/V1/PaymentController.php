<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReceiptVoucher;
use DB;
use App\Jobs\SendSmsJob;

class PaymentController extends BaseController
{

    public function storePayment(Request $request)
    {

        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'distributor_id' => 'required|exists:distributors,id',
            'tso_id' => 'required|exists:tso,id',
            'route_id' => 'required|exists:routes,id',
            'amount' => 'required|numeric|min:1',

        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['issue_date'] =  date('Y-m-d');
        $receiptVoucher = ReceiptVoucher::create($data);

            // $shop_data=  Shop::where('id',$request->shop_id)->first();

            // $shop_data['mobile_no'] = MasterFormsHelper::correctPhoneNumber($shop_data['mobile_no']);
            // $text = "Shop Name : $shop_data->company_name";
            // $text .= "\nContact Person : $shop_data->contact_person";
            // $text .= "\nRecived Payment $request->amount";
            // SendSmsJob::dispatch( $shop_data['mobile_no'] , $text);

        DB::commit();
        return $this->sendResponse([],'Successfully Submit');
    }
    catch (Exception $th) {
        DB::rollBack();
        return $this->sendError("Server Error!",['error'=> $th->getMessage()]);
    }
    }

    public function paymentList(Request $request)
    {
        $tso_id = auth()->user()->tso->id;
        $route_id = $request->route_id;
      $rvs =   ReceiptVoucher::where('status',1)->with('distributor')
        ->with('tso',function($query) use ($tso_id){
            $query->where('id',$tso_id);
        })
        ->whereHas('tso' , function($q) use ($tso_id){
            $q->where('id' , $tso_id);
        })
        ->when($route_id ,function($q) use ($route_id){
            $q->where('route_id' , $route_id);
        })
        ->latest('created_at')
        ->take(10)
        ->get();

        return $this->sendResponse($rvs,'Payment Data Successfully Fetched');
    }
}
