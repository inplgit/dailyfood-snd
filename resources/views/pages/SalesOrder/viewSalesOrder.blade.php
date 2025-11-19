@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper;
    $total_amount = 0;
    $total_qty = 0;
    $sheme_product = [];
@endphp

<style>
.table-bordered {
    border: 1px solid #ddd !important;
}
</style>

<div id="content" class="container print">
    <hr>
    <div class="model_content_custom">
        <div class="head_main">
            <h1 class="for-print">Sales Order</h1>
        </div>
        <div class="logo_snd">
            <h1 class="subHeadingLabelClass">{{ $so->distributor->distributor_name }}</h1>
            <h4 class="subHeadingLabelClass">{{ $so->distributor->address ?? '--' }}</h4>
        </div>
        <br>
        <div class="row align-items-center">
            <div class="col-lg-7 well">
                <table class="table table-bordered saleOrder_table">
                    <tr><th><h4>Sales Order</h4></th></tr>
                    <tr><th>Sale Order:</th><td>{{ $so->invoice_no }}</td></tr>
                    <tr><th>Sale Order Date:</th><td>{{ date("d-m-Y", strtotime($so->dc_date)) }}</td></tr>
                    <tr><th>Sale Order Delivery Date:</th><td>{{ date("d-m-Y", strtotime($so->delivery_date)) }}</td></tr>
                </table>
            </div>
            <div class="col-lg-5 well">
                <table class="table table-bordered saleOrder_table">
                    <tr><th>Distributor:</th><td>{{ $so->distributor->distributor_name }}</td></tr>
                    <tr><th>Order Booker:</th><td>{{ $so->tso->name }}</td></tr>
                    <tr><th>Shop:</th><td>{{ $so->shop->company_name }}</td></tr>
                    <tr><th>Invoice Type:</th><td>Cash</td></tr>
                </table>
            </div>
        </div>

        {{-- Order Details --}}
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-bordered Order_Details">
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
                        @foreach($so->saleOrderData as $key => $row)
                            @php
                                $total_amount += $row->total;
                                $total_qty += $row->qty;
                                $sheme_product[] = $row->sheme_product_id;
                                $sale_type = $master->uom_name($row->sale_type);
                            @endphp
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $row->product->product_name ?? '' }}</td>
                                <td>{{ $row->product_flavour->flavour_name ?? '' }}</td>
                                <td>{{ number_format($row->qty) }}</td>
                                <td>{{ $sale_type }}</td>
                                <td>{{ $row->rate }}</td>
                                <td>{{ number_format($row->discount, 2) }}</td>
                                <td>{{ number_format($row->discount_amount, 2) }}</td>
                                <td>{{ number_format($row->trade_offer_amount, 2) }}</td>
                                <td>{{ $row->scheme->scheme_name ?? '--' }}</td>
                                <td>{{ number_format($row->scheme_amount, 2) }}</td>
                                <td>{{ number_format($row->total, 2) }}</td>
                            </tr>
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

        {{-- Free Units Detail --}}
        @if (!empty($sheme_product))
            <div class="container" style="display: none;">
                <div class="row align-items-end">
                    <div class="col-lg-6">
                        <br><h4>Free Units Detail</h4><br>
                        <table class="table table-bordered Order_Details">
                            <thead><tr><th>Name</th><th>Pieces</th></tr></thead>
                            <tbody>
                                @foreach($so->saleOrderData as $row)
                                    @if($row->sheme_product_id != 0 && $row->offer_qty > 0)
                                        <tr>
                                            <td>{{ $row->SchmeProduct->product_name }}</td>
                                            <td>{{ $row->offer_qty }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-6">
                        <div class="notes">
                            <h2>Note</h2>
                            <p>Lorem ipsum dolor sit amet.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Scheme Product Details --}}
        @if($scheme_Pcs->isNotEmpty())
            <table class="table-bordered">
                <thead>
                    <tr><th>SCHEME PRODUCT</th><th>PIECES</th></tr>
                </thead>
                <tbody>
                    @foreach($scheme_Pcs as $order)
                        @if($order->scheme_data_pcs > 0)
                            <tr>
                                <td>{{ $order->product_name }}</td>
                                <td>{{ $order->scheme_data_pcs }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Not Found</p>
        @endif
    </div>
</div>





<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Invoice</title>

<style>
body{
    font-family: Arial, sans-serif;
    margin:0;
    padding:0;
    background:#fff;
}

.container{
    width: 900px;
    margin: auto;
    padding: 25px 35px;
    border:1px solid #555;
}

/* Header */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:10px;
}

.header-title{
    text-align:center;
    font-size:22px;
    font-weight:bold;
    text-decoration:underline;
}

.right-title{
    text-align:right;
    font-size:17px;
    font-weight:bold;
    text-decoration:underline;
}

/* Main Info Table */
.info-table{
    width:100%;
    border-collapse:collapse;
    font-size:14px;
    margin-top:20px;
}

.info-table td{
    border:1px solid #000;
    padding:6px;
}

/* Items Table */
.item-table{
    width:100%;
    border-collapse:collapse;
    margin-top:25px;
    font-size:14px;
}

.item-table th,
.item-table td{
    border:1px solid #000;
    padding:6px;
    text-align:center;
}

/* Left Box */
.left-box{
    width:45%;
    height:120px;
    border:1px solid #000;
    margin-top:20px;
    padding:10px;
    font-size:14px;
}

/* Right Summary */
.summary-box{
    width:50%;
    float:right;
    text-align:right;
    margin-top:20px;
}

.net-box{
    border:1px solid #000;
    display:inline-block;
    padding:8px 20px;
    font-size:22px;
    font-weight:bold;
}

/* Signature Lines */
.sign-row{
    width:100%;
    margin-top:60px;
    display:flex;
    justify-content:space-between;
}

.sign{
    width:260px;
    height:30px;
    border:1px solid #000;
}
</style>
</head>

<body>

<div class="container">

    <!-- HEADER -->
    <div class="header">
        <img src="LOGO.png" width="90"> 
        <div class="header-title">Sale Invoice</div>
        <div class="right-title">Farwa Traders</div>
    </div>

    <!-- INFO TABLE -->
    <table class="info-table">

        <tr>
            <td><b>Sale/Inv#</b></td>
            <td>MO-006716</td>
            <td><b>Invoice Date</b></td>
            <td>14-11-2025</td>
        </tr>

        <tr>
            <td><b>Cust Name</b></td>
            <td>Khan. Massalh</td>
            <td><b>Supply Date</b></td>
            <td>15-11-2025</td>
        </tr>

        <tr>
            <td><b>Address</b></td>
            <td>ghost market</td>
            <td><b>Due Date</b></td>
            <td>15-11-2025</td>
        </tr>

        <tr>
            <td><b>Contact</b></td>
            <td></td>
            <td><b>Tax Status</b></td>
            <td>GENERAL STORE</td>
        </tr>

        <tr>
            <td><b>Main Area</b></td>
            <td>MALIR</td>
            <td><b>Shop Type</b></td>
            <td>WHOLESALE</td>
        </tr>

        <tr>
            <td><b>Sub Area</b></td>
            <td>Ghanchi Market (Retail)</td>
            <td><b>Terms</b></td>
            <td>Cash</td>
        </tr>

        <tr>
            <td><b>Block</b></td>
            <td></td>
            <td><b>Bus Type</b></td>
            <td>ASM / TSO / SE / SM</td>
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

        <tr>
            <td>1</td>
            <td>MUSTARD POWDER 100g</td>
            <td>X72</td>
            <td>Cooking Club</td>
            <td>3</td>
            <td>120.00</td>
            <td>360.00</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>360.00</td>
        </tr>

        <tr>
            <td>2</td>
            <td>APPLE VINEGAR 315 ML</td>
            <td>X24</td>
            <td>Chtfo</td>
            <td>2</td>
            <td>180.00</td>
            <td>360.00</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>360.00</td>
        </tr>

        <tr>
            <td>3</td>
            <td>NATURAL JAMUN VINEGAR 31</td>
            <td>X24</td>
            <td>Chtfo</td>
            <td>2</td>
            <td>180.00</td>
            <td>360.00</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>360.00</td>
        </tr>

        <tr>
            <td colspan="14" style="text-align:right;"><b>Total:</b></td>
            <td><b>1,080.00</b></td>
        </tr>

    </table>


    <!-- LEFT BOX -->
    <div class="left-box">
        <b>For Inquiry/Complaint:</b><br>
        Contact/WhatsApp: 0300-0813906<br>
        Email us at: support@dailyfoodindustries.com
    </div>

    <!-- RIGHT SUMMARY -->
    <div class="summary-box">
        <p><b>Targeted Discount in %:</b> 0</p>
        <p><b>TOTAL NET AMOUNT</b></p>
        <div class="net-box">1,080.00</div>
    </div>

    <div style="clear:both;"></div>

    <!-- SIGNATURE BOXES -->
    <div class="sign-row">
        <div class="sign"></div>
        <div class="sign"></div>
        <div class="sign"></div>
    </div>

</div>

</body>
</html>
