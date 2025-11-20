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


<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 13px;
    }

    .invoice-box {
        width: 100%;
        padding: 20px 30px;
        border: 2px solid #000;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table th, table td {
        border: 1px solid #000;
        padding: 4px 6px;
        font-size: 13px;
    }

    .no-border td, .no-border th {
        border: none !important;
    }

    .header-logo img {
        width: 130px;
    }

    .bold { font-weight: bold; }
    .center { text-align: center; }
    .right { text-align: right; }
    .underline { text-decoration: underline; }
    .big { font-size: 18px; }
    .bigger { font-size: 22px; }
</style>

<div class="invoice-box">

    <!-- HEADER AREA -->
    <table class="no-border">
        <tr>
            <td width="33%" class="header-logo">
                <img src="https://i.postimg.cc/mrL6bW3L/daily.png">
            </td>

            <td width="33%" class="center bigger bold">
                Sale Invoice
            </td>

            <td width="33%" class="right underline bold">
                Farwa Traders
            </td>
        </tr>
    </table>

    <br>

    <!-- LEFT SIDE INFO -->
    <table class="no-border">
        <tr>
            <td width="33%">
                <table class="no-border">
                    <tr><td class="bold">SaleInv#</td></tr>
                    <tr>
                        <td style="border:1px solid #000; width:150px; padding:8px; font-size:16px;">
                            MO-006716
                        </td>
                    </tr>

                    <tr><td class="bold">Cust Name</td></tr>
                    <tr><td>Khan. Massalih</td></tr>

                    <tr><td class="bold">Address</td></tr>
                    <tr><td>ghost market</td></tr>

                    <tr><td class="bold">Contact</td></tr>
                    <tr><td>---</td></tr>

                    <tr><td class="bold">Main Area</td></tr>
                    <tr><td><b>MALIR</b></td></tr>

                    <tr><td class="bold">Sub Area</td></tr>
                    <tr><td>Ghanchi Market (Retail)</td></tr>

                    <tr><td class="bold">Block</td></tr>
                    <tr><td>---</td></tr>
                </table>
            </td>

            <!-- RIGHT SIDE -->
            <td width="67%">
                <table>
                    <tr>
                        <th>Invoice Date</th>
                        <td>14-11-2025</td>

                        <th>Supply Date</th>
                        <td>15-11-2025</td>

                        <th>Due Date</th>
                        <td>15-11-2025</td>
                    </tr>

                    <tr>
                        <th>Tax Status</th>
                        <td>GENERAL STORE</td>
                        <th>Shop Type</th>
                        <td>WHOLESALE</td>
                        <th>Terms</th>
                        <td>Cash</td>
                    </tr>

                    <tr>
                        <th>ASM</th>
                        <td></td>
                        <th>TSO</th>
                        <td></td>
                        <th>Bus Type</th>
                        <td></td>
                    </tr>

                    <tr>
                        <th>SE</th>
                        <td></td>
                        <th>SM</th>
                        <td colspan="3">Syed Manzar Akbar Zaidi</td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

    <br>

    <!-- PRODUCT TABLE -->
    <table>
        <thead>
            <tr class="bold center">
                <th>SR#</th>
                <th>Item Name</th>
                <th>Packing</th>
                <th>Brand</th>
                <th>Qty</th>
                <th>T.P</th>
                <th>Amount</th>
                <th>SCH D</th>
                <th>Fu</th>
                <th>T/O</th>
                <th>AMT</th>
                <th>ASD AMT</th>
                <th>%</th>
                <th>AddDisco</th>
                <th>Final Amount</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>1</td><td>MUSTARD POWDER 100g</td><td>X72</td><td>Cooking Club</td><td>3</td>
                <td>120.00</td><td>360.00</td><td>0</td><td>0</td><td>0</td><td>0</td>
                <td>360.00</td><td>0</td><td>0</td><td>360.00</td>
            </tr>

            <tr>
                <td>2</td><td>APPLE VINEGAR 315 ML</td><td>X24</td><td>Chifo</td><td>2</td>
                <td>180.00</td><td>360.00</td><td>0</td><td>0</td><td>0</td><td>0</td>
                <td>360.00</td><td>0</td><td>0</td><td>360.00</td>
            </tr>

            <tr>
                <td>3</td><td>NATURAL JAMUN VINEGAR 31</td><td>X24</td><td>Chifo</td><td>2</td>
                <td>180.00</td><td>360.00</td><td>0</td><td>0</td><td>0</td><td>0</td>
                <td>360.00</td><td>0</td><td>0</td><td>360.00</td>
            </tr>
        </tbody>
    </table>

    <br>

    <!-- SUMMARY AND NOTE -->
    <table class="no-border">
        <tr>
            <td width="50%" style="border:1px solid #000; height:120px; vertical-align:top; padding:10px;">
                <b>For Inquiry/Complaint,</b><br>
                Contact/WhatsApp: 0300-0813906<br>
                Email us at: support@dailyfoodindustries.com
            </td>

            <td width="50%" style="text-align:right; padding-right:20px;">
                <b>Targeted Discount in %</b> : 0
                <br><br>

                <table style="width:60%; float:right;">
                    <tr>
                        <th>Total Net Amount</th>
                        <td><b>1,080.00</b></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br><br><br>

    <!-- SIGNATURE BOXES -->
    <table class="no-border">
        <tr>
            <td width="33%" style="border:1px solid #000; height:40px;"></td>
            <td width="33%"></td>
            <td width="33%" style="border:1px solid #000; height:40px;"></td>
        </tr>
    </table>

</div>




@endsection
