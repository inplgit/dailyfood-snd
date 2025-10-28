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
