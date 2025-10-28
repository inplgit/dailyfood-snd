<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReceiptVoucherRequest;
use App\Models\Distributor;
use App\Models\ReceiptVoucher;
use App\Models\Route;
use App\Models\User;
use App\Helpers\MasterFormsHelper;
use Illuminate\Http\Request;

class ReceiptVoucherController extends Controller
{
    public function __construct()
    {
        $this->page = 'pages.ReceiptVoucher.';
        $this->master = new MasterFormsHelper();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $receiptVoucher = $this->master->get_receipt_voucher($request);

        if ($request->ajax()) :

            return view($this->page . 'TableData', compact('receiptVoucher'));
        endif;
        return view($this->page . 'IndexReceiptVoucher');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $distributors = $this->master->get_all_distributor_user_wise();
        $routes = Route::where('status', 1)->get();
        // delivery man from users table
        $deliveryMan = User::where('user_type', 3)->get();
        return  view($this->page . 'AddReceiptVoucher', compact('routes', 'deliveryMan', 'distributors'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreReceiptVoucherRequest $request)
    {
        // dd($request);
        $receiptVoucher = ReceiptVoucher::create($request->except('_token'));
        return response()->json(['success' => 'Receipt Voucher Created Successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $receipt_voucher= ReceiptVoucher::with('distributor', 'tso', 'shop', 'deliveryMan', 'route')->find($id);
        // dd($receipt_voucher);
        return  view($this->page.'viewReceiptVoucher',compact('receipt_voucher'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $record = ReceiptVoucher::findOrFail($id);
        $distributors = Distributor::where('status', 1)->get();
        $routes = Route::where('status', 1)->get();
        // delivery man from users table
        $deliveryMan = User::where('user_type', 3)->get();
        return  view($this->page . 'EditReceiptVoucher', compact('record', 'routes', 'deliveryMan', 'distributors'));
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
        $receiptVoucher = ReceiptVoucher::find($id);
        $receiptVoucher->update($request->except('_token'));
        return response()->json(['success' => 'Receipt Voucher Updated Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $receiptVoucher = ReceiptVoucher::where('id', $id)->update(['status' => 0]);
        return response()->json(['success' => 'ReceiptVoucher Deleted Successfully']);
    }
}
