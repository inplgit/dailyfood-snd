@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper;
@endphp
@extends('layouts.master')
@section('title', 'SND || Create New Sale Order')

@section('content')
<style>




.signature-area {
    display: flex;
    justify-content: space-between;
    margin-top: 30px;
}

.signature-item {
    text-align: center;
    width: 30%;
}

.signature-box {
    border-bottom: 1px solid #000;
    height: 40px;
    margin-bottom: 5px;
}

.table-bordered{border:1px solid #ddd !important;}
.logo.logo-flex-cont{display:flex;align-items:baseline;}
.logo-text p{color:#000;font-weight:bold;font-size:18px;}
body{font-family:Arial,sans-serif;margin:0;padding:0;background:#fff;}
.invoice-container{width:900px;margin:auto;padding:20px 0px;}
h2,h3,h4,p,table{margin:0;padding:0;}
.header {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: -30px;
}.logo img{width:80px;}
.title{text-align:center;font-size:20px;font-weight:bold;text-decoration:underline;color:#000;}
.right-title{text-align:right;font-size:18px;font-weight:bold;text-decoration:underline;color:#000;}
.section-table{width:100%;border-collapse:collapse;margin-top:10px;font-size:14px;}
.section-table td{border:1px solid #000;padding:5px;color:#000;    font-size: 13px;}
.section-table td b{color:#000;}
.section-table2 td{border:none !important;color:#000;    font-size: 13px;}
.item-table{width:100%;border-collapse:collapse;margin-top:15px;font-size:14px;border:1px solid #000;}
.item-table th,.item-table td{border:1px solid #000;padding:5px;text-align:center;color:#000;font-size:13px;}
.item-table td{border:none;}
.item-table2{width:100%;border-collapse:collapse;margin-top:15px;font-size:14px;border:none;margin-bottom:12px;}
.item-table2 th,.item-table2 td{border:1px solid #000;padding:5px;text-align:center;color:#000;font-size:13px;}
.item-table2 td{border:none;}
.summary-box{width:100%;margin-top:10px;display:flex;justify-content:space-between;align-items:flex-start;}
.left-box{width:28%;height:130px;border:1px solid #000;padding:10px;display:flex;align-items:flex-end;}
.left-box p{font-size:11px;color:#000;}
.right-summary{width:70%;float:right;text-align:right;color:#000;}
.signature-area{width:100%;margin-top:40px;display:flex;justify-content:space-between;}
.signature-box{border:1px solid #000;width:250px;height:30px;}


</style>




<div class="row mb">
    <div class="col-md-12">
        <div class="right" style="float: right">
            <!-- <button id="print" type="button" class="btn btn-success btn-sm right">Print</button> -->

            <button class="btn btn-success btn-sm right prinn pritns" onclick="printSection()">🖨️ Print</button>

        </div>





    </div>
</div>
<br>
<br>


<!-- old code -->
<!-- <div id="content" class="container print">
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
</div> -->



 <div id="content" class="card ptb container print" style="page-break-before: always">
   




<div class="invoice-container">

    {{-- ========== HEADER ========== --}}
    <div class="header">
        <div class="logo logo-flex-cont">
            <div class="logo_wrp">
                <a class="navbar-brand" href="{{ url('dashboard') }}">
                    <span class="brand-logo">
                        <!-- <img style="width: 175px;" src="{{ url('/public/assets/images/logo.png') }}"> -->
                        <img class="logo_m" src="{{ url('/public/assets/images/dailyfood_logo.jpeg') }}" onerror="this.onerror=null;this.src='{{ asset('logoo.png') }}'" />
                        <!-- <img class="logo_m hide" src="{{ asset('logo.png') }}"> -->
                    </span>
                </a>
            </div>
            <div class="logo-text">
                <p>Daily Food Industries</p>
            </div>
        </div>

        <div class="title">Sale Invoice</div>

        <div class="right-title">
            {{ $so->distributor->distributor_name }}
        </div>
    </div>


    <div class="row">

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">

            <!-- Row 1 -->
            <table class="section-table section-table2">
                <tr>
                     <td style=" text-decoration:underline;font-style:italic;color:#000;" width="20%"><b>Sale/Inv#</b></td>
                    <td style=" border:2px solid #000 !important;" width="30%"><b>{{$so->invoice_no}}</b></td>
        
                
                </tr>
        
                <tr>
                    <td><u>Cust Name</u></td>
                    <td><b>{{ $so->shop->company_name }}</b></td>
        
        
                </tr>
        
                <tr>
                    <td><u>Address</u></td>
                    <td><b>{{ $so->distributor->address ?? '--' }}</b></td>
        
        
                </tr>
        
                <tr>
                    <td><u>Contact</u></td>
                    <td><b>{{ $so->shop->phone ?? '--' }}</b></td>
        
        
                </tr>
        
                <tr>
                    <td><u>Main Area</u></td>
                    <td><b>{{ $so->shop->main_area ?? '--' }}</b></td>
        
        
                </tr>
        
                <tr>
                    <td><u>Sub Area</u></td>
                    <td><b>{{ $so->shop->route->route_name }}</b></td>
        
        
                </tr>
        
                <tr>
                    <td><u>Block</u></td>
                    <td><b>{{ $so->shop->block ?? '--' }}</b></td>
                </tr>
            </table>

        </div>

        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">

            <table class="section-table section-table2">
                <tr>
                    <td><u>Invoice Date</u></td>
                    <td><b>{{ date("d-m-Y", strtotime($so->dc_date)) }}</b></td>
        
                    <td><u>Supply Date</u></td>
                     <td><b>{{ \Carbon\Carbon::parse($so->dc_date)->addDay()->format('d-m-Y') }}</b></td>

                    <td><u>Due Date</u></td>
                  <td><b>{{ \Carbon\Carbon::parse($so->dc_date)->addDay()->format('d-m-Y') }}</b></td>

                </tr>
            </table>
        
            <table class="section-table">
                <tr>
                    <td style=" text-align:center;"><u>Tax Status</u></td>   
                    <td style=" text-align:center;"><u>Shop Type</u></td>
                    <td style=" text-align:center;"><u>Bus Type</u></u></td>
                    <td style=" text-align:center;"><u>Terms</td>
                </tr>
                <tr>
                    <td></td>
                    <td>{{ $so->shop->shopType->shop_type_name ?? '--' }}</td>
                    <td>{{ $so->shop->shopType->shop_type_name ?? '--' }}</td>
                    <td>{{ $so->payment_type ?? '--'}}</td>
                </tr>
            </table>

            <table class="section-table">
                <tr>
                    <td style=" text-align:center;"><u>ASM</u></td>
                    <td style=" text-align:center;"><u>TSO </u></td>
                    <td style=" text-align:center;"><u>SE</u></td>
                    <td style=" text-align:center;"><u>SM</u></td>
                </tr>
                <tr>
                    <td></td>
                      <td style=" text-align:center;"></td>
                    <td style=" text-align:center;">{{$so->tso->name }}</td>
                    <td></td>
                </tr>
            </table>

        </div>

    </div>


    <!-- ITEMS TABLE -->
    <table class="item-table">

        <tr>
            <th><u>S#</u></th>
            <th colspan="3"><u>Item Name</u></th>
            <th><u>Packing</u></u></th>
            <th><u>Brand</th>
            <th><u>Qty</u></th>
            <th><u>T.P</u></th>
            <th><u>Amount</u></th>
            <th><u>SC/B</u></th>
            <th><u>Eu</u></th>
            <th><u>T/O</u></th>
            <th><u>AMT</u></th>
            <th><u>A&D AMT</u></th>
            <th><u>%</u></th>
            <th><u>AddDisco</u></th>
            <th><u>Final Amount</u></th>
        </tr>

        @php
            $s = 1;
            $grand_total = 0;
        @endphp

        @foreach($so->saleOrderData as $row)
            @php
                $grand_total += $row->total;
            @endphp

            <tr>
                <td>{{ $s++ }}</td>
                <td colspan="3">{{ $row->product->product_name ?? '' }}</td>
                <td>{{ $row->product->packing_size ?? '' }}</td>
                <!-- <td>{{ $row->product_flavour->flavour_name ?? '' }}</td> -->
                <td>{{ $row->product->brand ?? '' }}</td>
                <td>{{ number_format($row->qty) }}</td>
                <td>{{ number_format($row->rate, 2) }}</td>
                <td>{{ number_format($row->total, 2) }}</td>

                <td>{{ number_format($row->discount, 2) }}</td>
                <td>{{ number_format($row->scheme_amount, 2) }}</td>
                <td>{{ number_format($row->trade_offer_amount, 2) }}</td>
                <td>{{ number_format($row->discount_amount, 2) }}</td>
                <td>{{ number_format($row->discount_amount, 2) }}</td>
                <td>{{ $so->discount_percent }}</td>

                <td>0</td> {{-- Static: Add/Less --}}

                <td>{{ number_format($row->total, 2) }}</td>
            </tr>

        @endforeach
    </table>

        
        <!-- SUMMARY -->
        <div class="summary-box">
            
            <div class="left-box">
                <p><b>For Inquiry/Complaint:</b><br>
                Contact/WhatsApp: 0300-0813906<br>
                Email us at: support@dailyfoodindustries.com
                </p>
            </div>
        
            <div class="right-summary">
                <table class="item-table2">
                    {{-- Totals Row --}}
                    <tr style="border-bottom:5px solid #000;">
                        <td></td>
                        <td style="text-align:center;"><u>{{ number_format($total_qty) }}</u></td>
                        <td style=" width:76px;text-align:right;"><u>{{number_format($grand_total,2)}}</u></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style=" text-align:right;width: 103px;">00</td>
                        <td style="text-align:right;"><u>0.00</u></td>
                        <td style="text-align:right;width: 124px;"><u>{{ number_format($total_amount, 2) }}</u></td>
                    </tr>
                </table>
                <table class="item-table" style="width:55%;float:right;border: none !important;">

                <tr>
                    <td style=" text-align:left;">
                         <p><b>Targeted Discount in %:</b> {{ $so->discount_percent }}</p>
                    </td>
                </tr>


                <tr style=" border:1px solid #000;">
                    <td style=" text-align:left;">
                        <p><b><u>TOTAL NET AMOUNT</u></b></p>
                    </td>

                    <td  style=" text-align:right;">
                        <p><b><u>{{ number_format($grand_total - $so->discount_amount, 2) }}</u></b></p>
                    </td>
                </tr>

            </table>


        </div>

    </div>
  @if($so->payment_type == 'credit')
    <!-- SIGNATURE BOXES -->
   <div class="signature-area">
    <div class="signature-item">
        <div class="signature-box"></div>
        <span>Order Booker</span>
    </div>

    <div class="signature-item">
        <div class="signature-box"></div>
        <span>Sale Person</span>
    </div>

    <div class="signature-item">
        <div class="signature-box"></div>
        <span>Shop Keeper</span>
    </div>
</div>

   @endif
</div>

<!-- new OLd copy design -->

 <!-- <div class="card ptb" style="page-break-before: always">
    <div class="invoice-container">

        {{-- ========== HEADER ========== --}}
        <div class="header">
            <div class="logo logo-flex-cont">
                <div class="logo_wrp">
                    <a class="navbar-brand" href="{{ url('dashboard') }}">
                        <span class="brand-logo">
                            <img class="logo_m" src="{{ url('/public/assets/images/dailyfood_logo.jpeg') }}" onerror="this.onerror=null;this.src='{{ asset('logoo.png') }}'" />
                            <img class="logo_m hide" src="{{ asset('logo.png') }}">
                        </span>
                    </a>
                </div>
                <div class="logo-text">
                    <p>Daily Food Industries</p>
                </div>
            </div>

            <div class="title">Sale Invoice</div>

            <div class="right-title">
                {{ $so->distributor->distributor_name }}
            </div>
        </div>


        <div class="row">

            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">

              
                <table class="section-table section-table2">
                    <tr>
                        <td style=" text-decoration:underline;font-style:italic;color:#000;" width="20%"><b>Sale/Inv#</b></td>
                        <td style=" border:2px solid #000 !important;" width="30%"><b>{{$so->invoice_no}}</b></td>
            
                    
                    </tr>
            
                    <tr>
                        <td><u>Cust Name</u></td>
                        <td><b>{{ $so->shop->company_name }}</b></td>
            
            
                    </tr>
            
                    <tr>
                        <td><u>Address</u></td>
                        <td><b>{{ $so->distributor->address ?? '--' }}</b></td>
            
            
                    </tr>
            
                    <tr>
                        <td><u>Contact</u></td>
                        <td><b>{{ $so->shop->mobile ?? '--' }}</b></td>
            
            
                    </tr>
            
                    <tr>
                        <td><u>Main Area</u></td>
                        <td><b>{{ $so->shop->main_area ?? '--' }}</b></td>
            
            
                    </tr>
            
                    <tr>
                        <td><u>Sub Area</u></td>
                        <td><b>{{ $so->shop->sub_area ?? '--' }}</b></td>
            
            
                    </tr>
            
                    <tr>
                        <td><u>Block</u></td>
                        <td><b>{{ $so->shop->block ?? '--' }}</b></td>
                    </tr>
                </table>

            </div>

            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">

                <table class="section-table section-table2">
                    <tr>
                        <td><u>Invoice Date</u></td>
                        <td><b>{{ date("d-m-Y", strtotime($so->dc_date)) }}</b></td>
            
                        <td><u>Supply Date</u></td>
                        <td><b>{{ date("d-m-Y", strtotime($so->delivery_date)) }}</b></td>
            
                        <td><u>Due Date</u></td>
                        <td><b>{{ date("d-m-Y", strtotime($so->delivery_date)) }}</b></td>
                    </tr>
                </table>
            
                <table class="section-table">
                    <tr>
                        <td style=" text-align:center;"><u>Tax Status</u></td>   
                        <td style=" text-align:center;"><u>Shop Type</u></td>
                        <td style=" text-align:center;"><u>Bus Type</u></u></td>
                        <td style=" text-align:center;"><u>Terms</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>GENERAL STORE</td>
                        <td>WHOLESALE</td>
                        <td>Cash</td>
                    </tr>
                </table>

                <table class="section-table">
                    <tr>
                        <td style=" text-align:center;"><u>ASM</u></td>
                        <td style=" text-align:center;"><u>{{ $so->tso->name }}</u></td>
                        <td style=" text-align:center;"><u>SE</u></td>
                        <td style=" text-align:center;"><u>SM</u></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td style=" text-align:center;">Syed Manzar Akbar Zaidi</td>
                        <td></td>
                    </tr>
                </table>

            </div>

        </div>


     
        <table class="item-table">

            <tr>
                <th><u>S#</u></th>
                <th colspan="3"><u>Item Name</u></th>
                <th><u>Packing</u></u></th>
                <th><u>Brand</th>
                <th><u>Qty</u></th>
                <th><u>T.P</u></th>
                <th><u>Amount</u></th>
                <th><u>SC/B</u></th>
                <th><u>Eu</u></th>
                <th><u>T/O</u></th>
                <th><u>AMT</u></th>
                <th><u>A&D AMT</u></th>
                <th><u>%</u></th>
                <th><u>AddDisco</u></th>
                <th><u>Final Amount</u></th>
            </tr>

            @php
                $s = 1;
                $grand_total = 0;
            @endphp

            @foreach($so->saleOrderData as $row)
                @php
                    $grand_total += $row->total;
                @endphp

                <tr>
                    <td>{{ $s++ }}</td>
                    <td colspan="3">{{ $row->product->product_name ?? '' }}</td>
            
                    <td>{{ $row->product_flavour->flavour_name ?? '' }}</td>
                    <td>{{ $row->product->brand ?? '' }}</td>
                    <td>{{ number_format($row->qty) }}</td>
                    <td>{{ number_format($row->rate, 2) }}</td>
                    <td>{{ number_format($row->total, 2) }}</td>

                    <td>{{ number_format($row->discount, 2) }}</td>
                    <td>{{ number_format($row->scheme_amount, 2) }}</td>
                    <td>{{ number_format($row->trade_offer_amount, 2) }}</td>
                    <td>{{ number_format($row->discount_amount, 2) }}</td>
                    <td>{{ number_format($row->discount_amount, 2) }}</td>
                    <td>{{ $so->discount_percent }}</td>

                    <td>0</td> {{-- Static: Add/Less --}}

                    <td>{{ number_format($row->total, 2) }}</td>
                </tr>

            @endforeach
        </table>

        
        
        <div class="summary-box">
            
            <div class="left-box">
                <p><b>For Inquiry/Complaint:</b><br>
                Contact/WhatsApp: 0300-0813906<br>
                Email us at: support@dailyfoodindustries.com
            </p>
        </div>
        
            <div class="right-summary">
                <table class="item-table2">
                    <tr style="border-bottom:5px solid #000;">
                        <td style="text-align:left;"><u>7.00</u></td>
                        <td></td>
                        <td colspan="3"></td>
                        <td></td>
                        <td><u>1,080.00</u></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><u>1,080.00</u></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td colspan="2"><u>0.00</u></td>
                        <td style="text-align:right;"><u>1,080.00</u></td>
                    </tr>
            
                </table>
                
                
                <table class="item-table" style="width:55%;float:right;border: none !important;">

                    <tr>
                        <td style=" text-align:left;">
                            <p><b>Targeted Discount in %:</b> {{ $so->discount_percent }}</p>
                        </td>
                    </tr>


                    <tr style=" border:1px solid #000;">
                        <td style=" text-align:left;">
                            <p><b><u>TOTAL NET AMOUNT</u></b></p>
                        </td>

                        <td  style=" text-align:right;">
                            <p><b><u>{{ number_format($grand_total - $so->discount_amount, 2) }}</u></b></p>
                        </td>
                    </tr>

                </table>


            </div>

        </div>

       
        <div class="signature-area">
            <div class="signature-box"></div>
            <div class="signature-box"></div>
            <div class="signature-box"></div>
        </div>

    </div>
</div> -->


@endsection







<script>
  function printSection() {
    // ✅ Print CSS dynamically add karna
    const printStyle = `
      @media print {
        @page{size:A4;margin:6mm 6mm 6mm 6mm !important;}
        
.table-bordered{border:1px solid #ddd !important;}
.logo.logo-flex-cont{display:flex;align-items:baseline;gap:20px}
.logo-text p{color:#000;font-weight:bold;font-size:18px;}
body{font-family:Arial,sans-serif;margin:0;padding:0;background:#fff;}
.invoice-container{width:900px;margin:auto;padding:20px 0px;}
h2,h3,h4,p,table{margin:0;padding:0;}
.header{display:flex;justify-content:space-between;align-items:baseline; margin-bottom: 0px;}
.logo img{width:80px;}
.title{text-align:center;font-size:20px;font-weight:bold;text-decoration:underline;color:#000;}
.right-title{text-align:right;font-size:18px;font-weight:bold;text-decoration:underline;color:#000;}
.section-table{width:100%;border-collapse:collapse;margin-top:10px;font-size:14px;}
.section-table td{border:1px solid #000;padding:5px;color:#000;    font-size: 13px;}
.section-table td b{color:#000;}
.section-table2 td{border:none !important;color:#000;    font-size: 13px;}
.item-table{width:100%;border-collapse:collapse;margin-top:15px;font-size:14px;border:1px solid #000;}
.item-table th,.item-table td{border:1px solid #000;padding:5px;text-align:center;color:#000;font-size:13px;}
.item-table td{border:none;}
.item-table2{width:100%;border-collapse:collapse;margin-top:15px;font-size:14px;border:none;margin-bottom:12px;}
.item-table2 th,.item-table2 td{border:1px solid #000;padding:5px;text-align:center;color:#000;font-size:13px;}
.item-table2 td{border:none;}
.summary-box{width:100%;margin-top:10px;display:flex;justify-content:space-between;align-items:flex-start;}
.left-box{width:28%;height:130px;border:1px solid #000;padding:10px;display:flex;align-items:flex-end;}
.left-box p{font-size:11px;color:#000;}
.right-summary{width:70%;float:right;text-align:right;color:#000;}
.signature-area{width:100%;margin-top:40px;display:flex;justify-content:space-between;}
.signature-box{border:1px solid #000;width:250px;height:30px;}

      }
    `;

    // ✅ Select element to print
    const printContent = document.getElementById('content').innerHTML;
    // ✅ Open new window for print
    const printWindow = window.open('', '', 'width=900,height=700');
    // ✅ Bootstrap 5 CSS link
    const bootstrapCSS = `<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">`;
    // ✅ Write content to print window
    printWindow.document.write(`
      <html>
      <head>
        <title>Print Preview</title>
        ${bootstrapCSS}
        <style>${printStyle}</style>
      </head>
      <body>
        ${printContent}
      </body>
      </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    // printWindow.close(); // optional
  }
</script>
<!-- </head>
<body> -->

