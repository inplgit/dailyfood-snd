@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper;
    $total_amount = 0;
    $total_qty = 0;
    $sheme_product = [];
@endphp

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
.header{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:10px;}
.logo img{width:80px;}
.title{text-align:center;font-size:20px;font-weight:bold;text-decoration:underline;color:#000;}
.right-title{text-align:right;font-size:18px;font-weight:bold;text-decoration:underline;color:#000;}
.section-table{width:100%;border-collapse:collapse;margin-top:10px;font-size:14px;}
.section-table td{border:1px solid #000;padding:5px;color:#000;}
.section-table td b{color:#000;}
.section-table2 td{border:none !important;color:#000;}
.item-table{width:100%;border-collapse:collapse;margin-top:15px;font-size:14px;border:1px solid #000;}
.item-table th,.item-table td{border:1px solid #000;padding:5px;text-align:center;color:#000;font-size:13px;}
.item-table td{border:none;}
.item-table2{width:100%;border-collapse:collapse;margin-top:15px;font-size:14px;border:none;margin-bottom:12px;}
.item-table2 th,.item-table2 td{border:1px solid #000;padding:5px;text-align:center;color:#000;font-size:13px;}
.item-table2 td{border:none;}
.summary-box{width:100%;margin-top:10px;display:flex;justify-content:space-between;align-items:flex-start;}
.left-box{width:33%;height:180px;border:1px solid #000;padding:10px;display:flex;align-items:flex-end;}
.left-box p{font-size:13px;color:#000;}
.right-summary{width:65%;float:right;text-align:right;color:#000;}
.signature-area{width:100%;margin-top:40px;display:flex;justify-content:space-between;}
.signature-box{border:1px solid #000;width:250px;height:30px;}


</style>

<!-- <div id="content" class="container print">
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
</div> -->





<div class="invoice-container">

    {{-- ========== HEADER ========== --}}
    <div class="header">
        <div class="logo logo-flex-cont">
            <div class="logo_wrp">
                <a class="navbar-brand" href="{{ url('dashboard') }}">
                    <span class="brand-logo">
                        <!-- <img style="width: 175px;" src="{{ url('/public/assets/images/logo.png') }}"> -->
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
                <tr style="border-bottom:5px solid #000;">
                    <td style="text-align:left;"><u>{{$total_qty}}</u></td>
                    <td></td>
                    <td colspan="3"></td>
                    <td></td>
                    <td><u>{{number_format($grand_total, 2)}}</u></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><u>00</u></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="2"><u>0.00</u></td>
                    <td style="text-align:right;"><u>{{number_format($grand_total, 2)}}</u></td>
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

