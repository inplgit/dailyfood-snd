@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper;
    $total_amount = 0;
    $total_qty = 0;
    $sheme_product = [];
@endphp

<style>
.table-bordered{border:1px solid #ddd !important;}







body{font-family:Arial,sans-serif;margin:0;padding:0;background:#fff;}
.invoice-container{width:900px;margin:auto;padding:20px 30px;border:1px solid #000;}
h2,h3,h4,p,table{margin:0;padding:0;}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;}
.logo img{width:80px;}
.title{text-align:center;font-size:20px;font-weight:bold;text-decoration:underline;}
.right-title{text-align:right;font-size:18px;font-weight:bold;text-decoration:underline;}
.section-table{width:100%;border-collapse:collapse;margin-top:10px;font-size:14px;}
.section-table td{border:1px solid #000;padding:5px;}
.item-table{width:100%;border-collapse:collapse;margin-top:15px;font-size:14px;}
.item-table th,.item-table td{border:1px solid #000;padding:5px;text-align:center;}
.summary-box{width:100%;margin-top:20px;}
.left-box{width:45%;height:120px;border:1px solid #000;display:inline-block;vertical-align:top;padding:10px;}
.right-summary{width:50%;float:right;text-align:right;}
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
        <div class="logo">
            <div class="logo_wrp">
                <a class="navbar-brand" href="{{ url('dashboard') }}">
                    <span class="brand-logo">
                        <!-- <img style="width: 175px;" src="{{ url('/public/assets/images/logo.png') }}"> -->
                        <img class="logo_m" src="{{ url('/public/assets/images/dailyfood_logo.jpeg') }}" onerror="this.onerror=null;this.src='{{ asset('logoo.png') }}'" />
                        <img class="logo_m hide" src="{{ asset('logo.png') }}">
                    </span>
                </a>
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
            <table class="section-table">
                <tr>
                    <td width="20%"><b>Sale/Inv#</b></td>
                    <td width="30%">{{ $so->invoice_no }}</td>
        
                
                </tr>
        
                <tr>
                    <td><b>Cust Name</b></td>
                    <td>{{ $so->shop->company_name }}</td>
        
        
                </tr>
        
                <tr>
                    <td><b>Address</b></td>
                    <td>{{ $so->distributor->address ?? '--' }}</td>
        
        
                </tr>
        
                <tr>
                    <td><b>Contact</b></td>
                    <td>{{ $so->shop->mobile ?? '--' }}</td>
        
        
                </tr>
        
                <tr>
                    <td><b>Main Area</b></td>
                    <td>{{ $so->shop->main_area ?? '--' }}</td>
        
        
                </tr>
        
                <tr>
                    <td><b>Sub Area</b></td>
                    <td>{{ $so->shop->sub_area ?? '--' }}</td>
        
        
                </tr>
        
                <tr>
                    <td><b>Block</b></td>
                    <td>{{ $so->shop->block ?? '--' }}</td>
                </tr>
            </table>

        </div>

        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">

            <table class="section-table">
                <tr>
                    <td><b>Invoice Date</b></td>
                    <td>{{ date("d-m-Y", strtotime($so->dc_date)) }}</td>
        
                    <td><b>Supply Date</b></td>
                    <td>{{ date("d-m-Y", strtotime($so->delivery_date)) }}</td>
        
                    <td><b>Due Date</b></td>
                    <td>{{ date("d-m-Y", strtotime($so->delivery_date)) }}</td>
                </tr>
            </table>
        
            <table class="section-table">
                <tr>
                    <td><b>Tax Status</b></td>    
                    <td><b>Shop Type</b></td>
                    <td><b>Bus Type</b></td>
                    <td><b>Terms</b></td>
                </tr>
                <tr>
                    <td></td>
                    <td>GENERAL STORE</td>
                    <td>WHOLESALE</td>
                    <td>Cash</td>
                </tr>
        
        
                <tr>
                    <td>ASM</td>
                    <td>{{ $so->tso->name }}</td>
                    <td>SE</td>
                    <td>SM</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>Syed Manzar Akbar Zaidi</td>
                    <td></td>
                </tr>
            </table>

        </div>

    </div>


    <!-- ITEMS TABLE -->
    <table class="item-table">

        <tr>
            <th>S#</th>
            <th colspan="2">Item Name</th>
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
            $s = 1;
            $grand_total = 0;
        @endphp

        @foreach($so->saleOrderData as $row)
            @php
                $grand_total += $row->total;
            @endphp

            <tr>
                <td>{{ $s++ }}</td>
                <td>{{ $row->product->product_name ?? '' }}</td>
                 <td></td>
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

        <tr  style="border-bottom:5px solid #000;">
             <td></td>
              <td></td>
               <td></td>
                <td></td>
                <td></td>
            <td  style="text-align:right;"><u>7.00</u></td>
            <td></td>
            <td><u>1,080.00</u></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><u>1,080.00</u></td>
            <td></td>
            <td><u>0.00</u></td>
            <td style="text-align:right;"><u>1,080.00</u></td>
        </tr>

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
            <p><b>Targeted Discount in %:</b> {{ $so->discount_percent }}</p>
            
            <table class="item-table" style=" width:55%;float:right;">

                <tr>
                    <td>
                        <p><b>TOTAL NET AMOUNT</b></p>
                    </td>

                    <td>
                        <p><b>{{ number_format($grand_total - $so->discount_amount, 2) }}</b></p>
                    </td>
                </tr>

            </table>


        </div>

    </div>

    <!-- SIGNATURE BOXES -->
    <div class="signature-area">
        <div class="signature-box"></div>
        <div class="signature-box"></div>
        <div class="signature-box"></div>
    </div>

</div>

