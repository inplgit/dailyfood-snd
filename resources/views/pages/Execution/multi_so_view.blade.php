@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper;
@endphp
@extends('layouts.master')
@section('title', 'SND || Create New Sale Order')

@section('content')
<div class="row mb">
    <div class="col-md-12">
        <div class="right" style="float: right">
            <button id="print" type="button" class="btn btn-success btn-sm right">Print</button>
        </div>
    </div>
</div>

<div id="content" class="container print">
    @foreach($sos as $key => $so)
    <div class="card ptb" style="page-break-before: always">
        <div class="logo_snd">
            <h1 class="subHeadingLabelClass">{{ $so->distributor->distributor_name }}</h1>
            <h4 class="subHeadingLabelClass">{{ $so->distributor->address ?? '--' }}</h4>
        </div>
        <br>
        <div class="row align-items-center">
            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 well">
                <table class="table table-bordered saleOrder_table">
                    <tr>
                        <th><h4>Sales Order</h4></th>
                    </tr>
                    <tr>
                        <th style="width: 30%;">Sale Order:</th>
                        <td>{{ $so->invoice_no }}</td>
                    </tr>
                    <tr>
                        <th>Sale Order Date:</th>
                        <td>{{ date("d-m-Y", strtotime($so->dc_date)) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 well">
                <table class="table table-bordered saleOrder_table">
                    <tr><th>Distributor:</th><td>{{ $so->distributor->distributor_name }}</td></tr>
                    <tr><th>TSO:</th><td>{{ $so->tso->name }}</td></tr>
                    <tr><th>Route:</th><td>{{ $so->shop->route->route_name }}</td></tr>
                    <tr><th>Sub Route:</th><td>{{ $so->shop->route->sub_route->name ?? '' }}</td></tr>
                    <tr><th>Shop:</th><td>{{ $so->shop->company_name }}</td></tr>
                    <tr><th>Invoice Type:</th><td>Cash</td></tr>
                </table>
            </div>
        </div>

        <hr>
        <div class="row">
            <div class="col-md-12">
                <h4>Order Details</h4>
                <table class="table table-bordered saleOrder_table">
                    <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Product</th>
                            <th>Flavour</th>
                            <th>QTY</th>
                            <th>Sale Type</th>
                            <th>Rate</th>
                            <th>Disc %</th>
                            <th>Disc Amount</th>
                            <th>Trade Offer</th>
                            <th>Scheme Product</th>
                            <th>Scheme Amount</th>
                            <th>Net Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_amount = 0;
                            $total_qty = 0;
                            $sheme_product = [];
                        @endphp

                        @foreach($so->saleOrderData as $key => $row)
                            @php
                                $total_amount += $row->total;
                                $total_qty += $row->qty;
                                $sale_type = $master->uom_name($row->sale_type);
                            @endphp
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $row->product->product_name ?? '--' }}</td>
                                <td>{{ $row->product_flavour->flavour_name ?? '' }}</td>
                                <td>{{ $row->qty }}</td>
                                <td>{{ $sale_type }}</td>
                                <td>{{ $row->rate }}</td>
                                <td>{{ number_format($row->discount, 2) }}</td>
                                <td>{{ number_format($row->discount_amount, 2) }}</td>
                                <td class="hide">{{ number_format($row->tax_amount, 2) }}</td>
                                <td>{{ number_format($row->trade_offer_amount, 2) }}</td>
                                <td>{{ $row->scheme->scheme_name ?? '--' }}</td>
                                <td>{{ number_format($row->scheme_amount, 2) }}</td>
                                <td>{{ number_format($row->total, 2) }}</td>
                            </tr>
                            @php $sheme_product[] = $row->sheme_product_id; @endphp
                        @endforeach

         {{-- Totals Row --}}
                     
			<tr class="bold">
                            <td colspan="3" class="text-right">Total Quantity</td>
                            <td colspan="7" >{{ number_format($total_qty) }}</td>

                          
                            

                          

                            <td colspan="1" class="text-right">Total Net Amount</td>
                            <td style="background: #FAFAFA;">{{ number_format($total_amount - $so->discount_amount, 2) }}</td>

                        </tr>
			
			<tr class="bold">
                            <td colspan="3" class="text-right"style="border: none !important;"></td>
                            <td style="border: none !important;"></td>

                            <td class="text-right"style="border: none !important;"></td>
                            <td style="border: none !important;"></td>

                            <td class="text-right"style="border: none !important;"></td>
                            <td style="border: none !important;"></td>
 			    <td class="text-right"style="border: none !important;"></td>
                            <td style="border: none !important;"></td>

       			    <td class="text-right">Bulk Discount</td>
                            <td style="background: #FAFAFA;">{{ number_format($so->discount_amount, 2) }} ({{ $so->discount_percent }}%)</td>
                        </tr>
                        <tr class="bold">
                           <td colspan="3" class="text-right"style="border: none !important;"></td>
                            <td style="border: none !important;"></td>

                            <td class="text-right"style="border: none !important;"></td>
                            <td style="border: none !important;"></td>

                            <td class="text-right"style="border: none !important;"></td>
                            <td style="border: none !important;"></td>
 			    <td class="text-right"style="border: none !important;"></td>
                            <td style="border: none !important;"></td>

                            <td class="text-right">Total Amount</td>
                            <td style="background: #FAFAFA;">{{ number_format($total_amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if (!empty($sheme_product))
        <br>
        <div class="container" style="display: none;">
            <div class="row">
                <div class="col-sm-6">
                    <h4>Free Units Detail</h4>
                    <table class="table table-bordered saleOrder_table">
                        <thead>
                            <tr><th>Name</th><th>Pieces</th></tr>
                        </thead>
                        <tbody>
                            @foreach($so->saleOrderData as $row)
                                @if($row->sheme_product_id != 0 && $row->offer_qty > 0)
                                    <tr>
                                        <td>{{ optional($row->SchmeProduct)->product_name ?? 'N/A' }}</td>
                                        <td>{{ $row->offer_qty }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endforeach
</div>
@endsection
