@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper;
@endphp
@extends('layouts.master')
@section('title', 'SND || Create New Sale Order')

@section('content')
<style>
 body{font-family:Arial,sans-serif;font-size:13px;}
.invoice-box{width:100%;padding:20px 30px;border:2px solid #000;}
table{width:100%;border-collapse:collapse;}
table th,table td{border:1px solid #000;padding:4px 6px;font-size:13px;}
.no-border td,.no-border th{border:none !important;}
.header-logo img{width:130px;}
.bold{font-weight:bold;}
.center{text-align:center;}
.right{text-align:right;}
.underline{text-decoration:underline;}
.big{font-size:18px;}
.bigger{font-size:22px;}

</style>





<div class="row mb">
    <div class="col-md-12">
        <div class="right" style="float: right">
            <button id="print" type="button" class="btn btn-success btn-sm right">Print</button>
        </div>
    </div>
</div>
<br>
<br>
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



<div id="content" class="container print">
@foreach($sos as $key => $so)
<div class="invoice-container" style="page-break-before: always">

    <div class="header">
        <div class="logo">
            <img src="https://i.postimg.cc/mrL6bW3L/daily.png">
        </div>
        <div class="title">Sale Invoice</div>
        <div class="right-title">
            {{ $so->distributor->distributor_name ?? 'Farwa Traders' }}
        </div>
    </div>

    <!-- Row 1 -->
    <table class="section-table">
        <tr>
            <td><b>Sale/Inv#</b></td>
            <td>{{ $so->invoice_no }}</td>

            <td><b>Invoice Date</b></td>
            <td>{{ date('d-m-Y', strtotime($so->dc_date)) }}</td>
        </tr>

        <tr>
            <td><b>Cust Name</b></td>
            <td>{{ $so->shop->company_name ?? '--' }}</td>

            <td><b>Supply Date</b></td>
            <td>{{ date('d-m-Y', strtotime($so->dc_date)) }}</td>
        </tr>

        <tr>
            <td><b>Address</b></td>
            <td>{{ $so->distributor->address ?? 'ghost market' }}</td>

            <td><b>Due Date</b></td>
            <td>{{ date('d-m-Y', strtotime($so->dc_date)) }}</td>
        </tr>

        <tr>
            <td><b>Contact</b></td>
            <td>{{ $so->shop->mobile ?? '--' }}</td>

            <td><b>Tax Status</b></td>
            <td>GENERAL STORE</td> <!-- static -->
        </tr>

        <tr>
            <td><b>Main Area</b></td>
            <td>{{ $so->shop->route->route_name ?? 'MALIR' }}</td>

            <td><b>Shop Type</b></td>
            <td>{{ $so->shop->type ?? 'WHOLESALE' }}</td>
        </tr>

        <tr>
            <td><b>Sub Area</b></td>
            <td>{{ $so->shop->route->sub_route->name ?? 'Ghanchi Market (Retail)' }}</td>

            <td><b>Terms</b></td>
            <td>Cash</td> <!-- static -->
        </tr>

        <tr>
            <td><b>Block</b></td>
            <td>{{ $so->shop->block ?? '--' }}</td>

            <td><b>Bus Type</b></td>
            <td>ASM / TSO / SE / SM</td> <!-- static -->
        </tr>
    </table>

    <!-- ITEMS TABLE -->
    <table class="item-table">
        <tr>
            <th>S#</th>
            <th>Item Name</th>
            <th>Packing</th>
            <th>Brand</th>
            <th>Qty</th>
            <th>T.P</th>
            <th>Amount</th>
            <th>SC/B</th>
            <th>Eu</th>
            <th>T/O</th>
            <th>AMT</th>
            <th>A&D AMT</th>
            <th>%</th>
            <th>Add/less</th>
            <th>Final Amount</th>
        </tr>

        @php 
            $total_amount = 0; 
            $c = 1;
        @endphp

        @foreach($so->saleOrderData as $row)
        @php 
            $total_amount += $row->total; 
        @endphp

        <tr>
            <td>{{ $c++ }}</td>
            <td>{{ $row->product->product_name ?? '--' }}</td>
            <td>{{ $row->product->packing ?? 'X24' }}</td>
            <td>{{ $row->product->brand->name ?? '--' }}</td>
            <td>{{ $row->qty }}</td>
            <td>{{ number_format($row->rate,2) }}</td>

            <td>{{ number_format($row->rate * $row->qty,2) }}</td>

            <td>0</td> <!-- static -->
            <td>0</td> <!-- static -->
            <td>{{ number_format($row->trade_offer_amount ?? 0,2) }}</td>
            <td>{{ number_format($row->discount_amount ?? 0,2) }}</td>
            <td>{{ number_format($row->scheme_amount ?? 0,2) }}</td>

            <td>{{ number_format($row->discount ?? 0,2) }}</td>

            <td>0</td> <!-- static Add/Less -->

            <td>{{ number_format($row->total,2) }}</td>
        </tr>
        @endforeach

        <tr>
            <td colspan="14" style="text-align:right;"><b>Total:</b></td>
            <td><b>{{ number_format($total_amount,2) }}</b></td>
        </tr>
    </table>


    <!-- SUMMARY BOX -->
    <div class="summary-box">
        <div class="left-box">
            <p><b>For Inquiry/Complaint:</b><br>
            Contact/WhatsApp: 0300-0813906<br>
            Email us at: support@dailyfoodindustries.com
            </p>
        </div>

        <div class="right-summary">
            <p><b>Targeted Discount in %:</b> {{ $so->discount_percent ?? 0 }}</p>
            <p><b>TOTAL NET AMOUNT</b></p>

            <p style="font-size:20px; border:1px solid #000; padding:5px; display:inline-block;">
                <b>{{ number_format($total_amount - ($so->discount_amount ?? 0),2) }}</b>
            </p>
        </div>
    </div>

    <!-- SIGNATURES -->
    <div class="signature-area">
        <div class="signature-box"></div>
        <div class="signature-box"></div>
        <div class="signature-box"></div>
    </div>

</div>
@endforeach
</div>





@endsection
