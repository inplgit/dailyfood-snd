<?php
use App\Helpers\MasterFormsHelper;
use App\Models\SchemeProduct;
$master = new MasterFormsHelper();
?>

@extends('layouts.master')
@section('title', 'SND || Edit Sales Order')
@section('content')
<style>
.table-responsive {
    height: auto !important;
}
</style>

    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit SALE ORDER</h4>
                    </div>
                    <div class="card-body">
                        <form id="subm_rest" method="POST" action="{{ route('sale.update', $record->id) }}" class="form">
                            @csrf
                            @method('patch')
                            <div class="col-md-12">
                                <section class="panel">
                                    <div class="sale_order_detsils">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="main_head">
                                                    <h2>Sales Order </br>Details</h2>
                                                </div>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="panel-body">
                                                    <!--Own Text -->
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="control-label"> Invoice # </label>
                                                            <input readonly name="invoice_no" class=" form-control"
                                                                tabindex="1" type="text" id="onrecord"
                                                                required="required" value="{{ $record->invoice_no }}">
                                                            <span id="ord"></span>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <label class="control-label"> Order Date </label>
                                                            <input name="dc_date" class=" form-control"
                                                                value="{{ $record->dc_date }}" tabindex="10" type="date"
                                                                id="dcdate">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="control-label">Deliveryder Date </label>
                                                            <input name="delivery_date" class=" form-control"  value="{{ $record->delivery_date }}" tabindex="10" type="date" id="delivery_date">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">

                                                                <label
                                                                    class="col-lg-4 control-label">Distributor</label><br>

                                                                <select name="distributor_id" class="form-control select2"
                                                                    id="distribuotr_id" tabindex="-1" aria-hidden="true"
                                                                    onchange="get_tso()">
                                                                    <option value="" selected="">All</option>
                                                                    @foreach ($master->get_all_distributor_user_wise() as $distributor)
                                                                        <option
                                                                            {{ $record->distributor_id == $distributor->id ? 'selected' : '' }}
                                                                            value="{{ $distributor->id }}">
                                                                            {{ $distributor->distributor_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="tso2">
                                                                <label class="col-lg-2 control-label">TSO</label>
                                                                <select class="form-control select2"  onchange="get_route_by_tso()" name="tso_id"
                                                                    id="tso_id">
                                                                    <option value="">Select a TSO: </option>
                                                                    @foreach ($master->get_all_tso_by_distributor_id($record->distributor_id) as $row)
                                                                        <option
                                                                            {{ $record->tso_id == $row->id ? 'selected' : '' }}
                                                                            value="{{ $row->id }}">{{ $row->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 col-12">
                                                        <div class="form-group">
                                                            <label class="control-label" >Route</label>
                                                            <select onchange="get_shop_by_route()" id="route_id" name="route_id" class="select2 form-control form-control-lg">

                                                    
                                                            @foreach ($master->get_all_route_by_shop_ids([$record->route_id]) as $row)
                                                                        <option
                                                                            {{ $record->route_id == $row->id ? 'selected' : '' }}
                                                                            value="{{ $row->id }}">{{ $row->route_name }}
                                                                        </option>
                                                                    @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                        <div class="col-md-3">

                                                            <label class="col-lg-2 control-label">Shop</label>

                                                            <select class="form-control shop_id" id="shop_id"
                                                                name="shop_id" required="">
                                                                <option value="">Select a Shop </option>
                                                                @foreach ($shops as $shop)
                                                                    <option
                                                                        {{ $record->shop_id == $shop->id ? 'selected' : '' }}
                                                                        value="{{ $shop->id }}">
                                                                        {{ $shop->company_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
									
                                                        <div class="col-md-3 hide">
                                                            <label class=" control-label"> DC No. </label>
                                                            <input name="dc_no" class=" form-control"
                                                                value="{{ $record->dc_no }}" tabindex="10" type="number"
                                                                id="number">
                                                            <span id="serial"></span>
                                                        </div>
                                                        <div class="col-md-3 hide">
                                                            <div style=" ">
                                                                <label class="control-label"> LPO # </label>
                                                                <input name="lpo_no" class=" form-control"
                                                                    value="{{ $record->lpo_no }}" tabindex="10"
                                                                    type="number" id="lpo">
                                                            </div>
                                                        </div>


                                                        <div class="col-md-3 hide">
                                                            <label class="control-label"> Total Amount </label>
                                                            <input id="total_amount" value="{{ $record->total_amount }}"
                                                                class=" form-control" step="any" name="total_amount"
                                                                type="number" tabindex="-1">
                                                        </div>

                                                        {{-- <div id="subcustomer"></div> --}}


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>


                            <div class="item_details">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="main_head">
                                            <h2>Item Details</h2>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                        <table width="100%" cellpadding="5" class="table table-bordered table-striped "
                                            id="acart">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <div>Product Name</div>
                                                    </th>
                                                    <th>
                                                        <div>Flavour</div>
                                                    </th>
                                                    <th>
                                                        <div>Sale Type</div>
                                                    </th>
                                                    <th>
                                                        <div>QTY</div>
                                                    </th>
                                                    <th>
                                                        <div>Rate</div>
                                                    </th>
                                                    <th>
                                                        <div>Disc</div>
                                                    </th>
                                                     <!-- <th>
                                                        <div>Disc Amount</div>
                                                    </th> -->

                                                    <th class="hide">
                                                        <div>Tax (%)</div>
                                                    </th>
                                                    <!-- <th>
                                                        <div>Trade Offer </div>
                                                    </th> -->
                                                     <th class="hide">
                                                        <div>Scheme Product </div>
                                                    </th>
                                                    <th class="hide">
                                                        <div>Scheme Amount </div>
                                                    </th>
                                                    <th>
                                                        <div>Scheme Product Pcs </div>
                                                    </th>
                                                    <th>
                                                        <div>Scheme Pcs </div>
                                                    </th>
                                                    <th>
                                                        <div>SCH D</div>
                                                    </th>
                                                    <th>
                                                        <div>Total </div>
                                                    </th>
                                                    <th><button type="button" class="btn btn-primary"
                                                    id="add-more" onclick="addMore()">Add More</button></th>
                                                </tr>
                                            </thead>
                                            <input type="hidden" class="remove_id" name="remove_id" value="">
                                            <tbody id="appendRow">
                                                @foreach ($record->saleOrderData as $key => $data)
                                                    <tr id="removeRow{{ $key }}">
                                                        <input type="hidden" class="data_id" name="sale_order_data_id[]"
                                                            value="{{ $data->id }}">
                                                        <td style="width: 28%;">
                                                            <select style="width:290px !important;" onchange="get_product_price(this); get_flavour(this); get_scheme_product(this); get_total_caton_and_qty();" name="product_id[]"
                                                                tabindex="-1" required
                                                                class="combobox form-control product_id" aria-hidden="true">
                                                                <option value="">Select a Product</option>
                                                                @foreach ($products as $product)
                                                                    <option data-product_price="{{$master->get_product_price($product->id)}}" data-flavour="{{$product->product_flavour}}"
                                                                        {{ $data->product_id == $product->id ? 'selected' : '' }}
                                                                        data-url="{{ route('sale-order.table-row', $product->id) }}"
                                                                        value="{{ $product->id }}">
                                                                        {{ $product->product_name }}</option>
                                                                @endforeach
                                                            </select>

                                                        </td>
                                                        <td>
                                                            <select style="width:130px !important;" name="flavour_id[]"  id="" class="form-control flavour">
                                                                @foreach ($data->product->product_flavour as $flavour)
                                                                    <option value="{{$flavour->id}}" {{$flavour->id == $data->flavour_id ? 'selected' : ''}} >{{$flavour->flavour_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select style="width:130px !important;"name="sale_type[]" onchange="get_rate(this)" id="" class="form-control sale_type">
                                                                @foreach ($data->product->product_price as $product_price)
                                                                    <option value="{{$product_price->uom_id}}" {{$product_price->uom_id == $data->sale_type ? 'selected' : ''}} >{{$product_price->uom->uom_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input style="width:130px !important;"onkeyup="calc(this); get_scheme_product(this); 
                                                            get_total_caton_and_qty();" onblur="calc(this); get_scheme_product(this); get_total_caton_and_qty(this)"
                                                                class="form-control data-quantity" type="number"
                                                                min="0" name="qty[]"
                                                                value="{{ (int) $data->qty ?? 0 }}">
                                                        </td>
                                                        <td>
                                                            <input style="width:130px !important;" step="any" onkeyup="calc(this)"
                                                                onblur="calc(this)" class="form-control data-rate"
                                                                type="number" min="0" name="rate[]"
                                                                value="{{ $data->rate ?? 0 }}">
                                                        </td>
                                                        <td>
                                                            <input style="width:130px !important;" onkeyup="calc(this)" onblur="calc(this)"
                                                                class="form-control data-discount" type="text"
                                                                name="data_discount[]"
                                                                value="{{ $data->discount ?? 0 }}">
                                                            {{-- <input class="data-discount-amount" type="hidden" name="data_discount_amount[]" value="{{ $data->discount_amount ?? 0 }}"> --}}
                                                        </td>
                                                    <td  class="hide">
                                                            <input style="width:130px !important;"class="form-control data-discount-amount"
                                                                onkeyup="calc(this , true)" type="integer"
                                                                name="data_discount_amount[]" value="{{ $data->discount_amount ?? 0 }}">
                                                        </td> 
                                                        <td class="hide">
                                                            <input style="width:130px !important;" onkeyup="calc(this)" onblur="calc(this)" readonly
                                                                class="form-control data-tax-percent" type="text"
                                                                name="data_tax_percent[]"
                                                                value="{{ $data->tax_percent ?? 0 }}">
                                                            <input class="data-tax-amount" type="hidden"
                                                                name="data_tax_amount[]"
                                                                value="{{ $data->tax_amount ?? 0 }}">
                                                        </td>
   								<td style="width:130px !important;" class="hide">
                                                            <input onkeyup="calc(this)" class="form-control trade_offer_amount" type="number" name="trade_offer_amount[]" value="{{$data->trade_offer_amount ?? 0}}">
                                                        </td> 
                                                        @php
                                                             $scheme_products = SchemeProduct::Status()->Active()
                                                                ->join('scheme_product_data' , 'scheme_product_data.scheme_id' , 'scheme_product.id')
                                                                ->whereRaw("FIND_IN_SET(?, scheme_product_data.product_id)", [$data->product_id])
                                                                ->where('scheme_product_data.qty' , '<=' , $data->qty)
                                                                ->select('scheme_product.scheme_name','scheme_product.id as scheme_id', 'scheme_product_data.id as scheme_data_id' , 'scheme_product_data.qty' , 'scheme_product_data.scheme_amount')
                                                                ->get();
                                                        @endphp
                                                         <td class="hide">
                                                            <select style="width: 130px !important;" name="scheme_id[]" onchange="get_scheme_amount(this);" id="" class="form-control scheme_product">
                                                                <option value="">Select</option>
                                                                @foreach ($scheme_products as $scheme_product)
                                                                    <option value="{{$scheme_product->scheme_id}},{{$scheme_product->scheme_data_id}}" data-scheme_amount="{{$scheme_product->scheme_amount}}" data-qty="{{$scheme_product->qty}}" {{$scheme_product->scheme_id == $data->scheme_id ? 'selected' : ''}}>{{$scheme_product->scheme_name}} -- qty {{$scheme_product->qty}}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td class="hide">
                                                            <input style="width: 130px !important;" readonly class="form-control scheme_amount" step="any" type="number" name="scheme_amount[]" value="{{$data->scheme_amount ?? 0}}">
                                                        </td>
                                                        <td class="hide">
                                                            <input style="width: 130px !important;" readonly class="form-control scheme_qty" step="any" type="number" name="scheme_qty[]" value="{{$data->scheme_qty ?? 0}}">
                                                        </td>
                                                        <td>
                                                            <select style="width: 130px !important;" name="scheme_id_pcs[]" onchange="get_scheme_pcs(this);" id="" class="form-control scheme_product_pcs">
                                                                <option value="">Select</option>
                                                                <!-- Scheme PCS options will be populated dynamically -->
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input style="width: 130px !important;" readonly class="form-control scheme_pcs" step="any" type="number" name="scheme_pcs[]" value="{{$data->scheme_data_pcs ?? 0}}">
                                                        </td>
                                                        <td>
                                                            <input style="width: 130px !important;" readonly class="form-control sch_d" type="number" name="sch_d[]" value="{{$data->scheme_amount ?? 0}}">
                                                        </td>
                                                        <td class="hide">
                                                            <input style="width: 130px !important;" readonly class="form-control scheme_pcs_total" step="any" type="number" name="scheme_product_pcs_total[]" value="{{$data->scheme_product_pcs_total ?? 0}}">
                                                        </td>
                                                        <td class="hide">
                                                            <input style="width: 130px !important;" onkeyup="calc(this)" readonly
                                                                class="form-control data-tax-percent" type="number"
                                                                name="data_tax_percent[]"
                                                                value="{{ $data->tax_percent ?? 0 }}">
                                                            <input class="data-tax-amount" type="hidden"
                                                                name="data_tax_amount[]"
                                                                value="{{ $data->tax_amount ?? 0 }}">
                                                        </td>
                                                        <td>
                                                            <input style="width: 190px !important;" readonly class="form-control data-total" type="number"
                                                                name="data_total[]" value="{{ $data->total ?? 0 }}">
                                                        </td>

                                                        <td>
                                                            @if ($key == 0)
                                                                 <button type="button"
                                                                    onclick="removeRow({{ $key }},this)"
                                                                    class="btn btn-danger btn-xs">-</button>
                                                            @else
                                                                <button type="button"
                                                                    onclick="removeRow({{ $key }},this)"
                                                                    class="btn btn-danger btn-xs">-</button>
                                                            @endif
                                                        </td>





                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sales OrderDetails -->
                            <div class="Sales_Order_Details">
                                <section class="panel">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="main_head">
                                                    <h2>Sales Order <br> Details</h2>
                                                </div>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="row">

                                                    <div class="col-md-3">
                                                        <label class="control-label"> Products Subtotal </label>
                                                        <input step="any" id="products_subtotal"
                                                            value="{{ $record->products_subtotal ?? 0 }}"
                                                            name="products_subtotal" type="number"
                                                            class="total-box form-control" id="products_subtotal">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="control-label">Bulk Discount</label>
                                                        <input id="discount_percent" name="discount_percent"
                                                            value="{{ $record->discount_percent ?? 0 }}" type="text"
                                                            class="form-control" tabindex="15" accesskey="d">
                                                        <input id="discount_amount" name="discount_amount" type="hidden"
                                                            class="form-control" value="{{ $record->discount_amount }}">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="control-label"> Execution </label>
                                                        <select name="excecution" class="form-control">
                                                            <option {{ $record->execution ? 'selected' : '' }}
                                                                value="1">
                                                                Yes</option>
                                                            <option {{ !$record->execution ? 'selected' : '' }}
                                                                value="0" selected="">No</option>
                                                        </select>
                                                    </div>


                                                    <div class="col-md-3">
                                                        <label class="control-label"> Execution Date </label>
                                                        <input name="excecution_date"
                                                            value="{{ $record->excecution_date ?? '' }}" tabindex="10"
                                                            type="date" id="date" class="form-control"
                                                            autocomplete="new-password">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="control-label"> Total Carton </label>
                                                        <input type="number" name="total_carton" id="packing"
                                                            value="{{ $record->total_carton ?? 0 }}"
                                                            class="form-control">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="control-label"> Pending Amount </label>
                                                        <input id="pending_amount" name="pending_amount"
                                                            class="form-control" type="number" min="0"
                                                            value="{{ $record->pending_amount ?? 0 }}"
                                                            id="pending_amount" readonly="">
                                                    </div>

                                                    <div class="col-md-3 hide">
                                                        <div class="invoicetax">
                                                            <label class="control-label"> Tax Applied </label>
                                                            <input id="tax_applied" name="tax_applied" type="text"
                                                                id="tax" value="{{ $record->tax_applied }}"
                                                                class="form-control" readonly="">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="control-label"> Payment </label>
                                                        <select name="payment_type" class="form-control">
                                                            <option {{ $record->payment_type == '2' ? 'selected' : '' }}
                                                                value="2" selected="">Credit</option>
                                                            <option {{ $record->payment_type == '1' ? 'selected' : '' }}
                                                                value="1">Cash</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="control-label"> Total Pcs </label>
                                                        <input name="total_pcs" type="number" id="total_pcs"
                                                            value="{{ $record->total_pcs ?? 0 }}" class="form-control">
                                                    </div>


                                                    <div class="col-md-3 hide">
                                                        <div id="oldr">

                                                            <label class="control-label"> Old Receivable </label>
                                                            <input id="old_receivable"
                                                                value="{{ $record->old_receivable ?? 0 }}"
                                                                name="old_receivable" type="text" class="form-control"
                                                                id="old_receivable" readonly="">
                                                        </div>
                                                    </div>


                                                    <div class="col-md-3 hide">
                                                        <label class="control-label"> Freight Charges </label>
                                                        <input id="freight_charges" name="freight_charges" accesskey="f"
                                                            type="number" min="0"id="shipping"
                                                            value="{{ $record->freight_charges ?? 0 }}" tabindex="15"
                                                            class="form-control">
                                                    </div>


                                                    <div class="col-md-3 hide">
                                                        <label class="control-label"> Cost Center </label>
                                                        <select name="cost_center" id="combobox"
                                                            class="teacher form-control">
                                                            <option {{ $record->cost_center == '1' ? 'selected' : '' }}
                                                                value="1">Embroidery</option>
                                                            <option {{ $record->cost_center == '4' ? 'selected' : '' }}
                                                                value="4">Knitting</option>
                                                            <option {{ $record->cost_center == '12' ? 'selected' : '' }}
                                                                value="12">Factory Expenses</option>
                                                        </select>
                                                    </div>


                                                    <div class="col-md-6">
                                                        <label class="control-label"> Notes </label>
                                                        <textarea name="notes" class="form-control">{{ $record->notes ?? '' }}</textarea>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="control-label"> Transport Details </label>
                                                        <textarea name="transport_details" class="form-control">{{ $record->transport_details ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>

                            <div class="row">
                                <div class="col-md-4"></div>
                                <div class="col-md-4"></div>
                                <div class="col-md-4">
                                    <div class="total_button">

                                    
                                       <div class="slab_discount_fields">
                                        <label class="control-label hide">Slab ID</label>
                                        <input type="hidden" id="slab_id"  value="{{ $record->slab_id }}"  name="slab_id" class="form-control " readonly>
                                        
                                        <label class="control-label hide">Slab Details ID</label>
                                        <input type="hidden" id="slab_details_id"  value="{{ $record->slab_details_id }}"  name="slab_details_id" class="form-control " readonly>
                                        
                                        <label class="control-label ">Slab Percentage</label>
                                        <input type="text" id="slab_percentage"   value="{{ $record->slab_percentage }}" name="slab_percentage" class="form-control" readonly>
                                        
                                        <label class="control-label">Slab Discount Amount</label>
                                        <input type="text" id="slab_amount"  value="{{ $record->slab_amount }}"  name="slab_amount" class="form-control" readonly>

                                        <label class="control-label">Total</label>
                                        <input id="net_total" name="total" type="number"
                                            value="{{ $record->total_amount }}" class="change form-control"
                                            id="total" readonly="readonly" required="">
                                    </div>

                                    <div class="button_create text-right">
                                        <input name="submit2" type="submit" id="submit2" accesskey="o"
                                            tabindex="15" class="btn btn-warning" value="Submit">
                                        <input name="Clear" type="reset" id="Reset" class="btn btn-info">
                                    </div>
                                </div>

                            </div>



                        </form>



                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Floating Label Form section end -->

@endsection
@section('script')
<script>
    $(document).on('change', '#shop_id', function() {
        var newShopId = $(this).val();
        var previousShopId = window.previousShopId || null;
        window.previousShopId = newShopId;
        $(this).data('previous-value', newShopId);
        
        if (newShopId !== previousShopId && previousShopId !== null) {
            console.log('Shop changed! Clearing previous slab discount...');
            resetSlabDiscountFields();
        }
        
        setTimeout(function() {
            calculation();
        }, 100);
    });

    function calculation() {
        var $total = 0;

        $('.data-total').each(function (index) {
            $total += parseFloat(this.value) || 0;
        });

        $('#products_subtotal').val($total.toFixed(2));

        var freight_charges = parseFloat($('#freight_charges').val()) || 0;
        var discount_percent = parseFloat($('#discount_percent').val()) || 0;

        var discount_amount = ($total / 100) * discount_percent;
        var $net_total_before_slab = $total + freight_charges - discount_amount;
        
        // Step 1: Calculate base total (bulk discount se pehle)
        var baseTotal = $total + freight_charges;
        $('#total_amount').val(baseTotal.toFixed(2));
        
        // Step 2: Apply bulk discount
        var bulkDiscountAmount = discount_amount;
        $('#discount_amount').val(bulkDiscountAmount.toFixed(2));
        
        // Step 3: Calculate slab discount
        calculateSlabDiscount(baseTotal, bulkDiscountAmount, $net_total_before_slab);
    }

    function calculateSlabDiscount(baseTotal, bulkDiscountAmount, netTotalBeforeSlab) {
        var shopId = $('#shop_id').val();
        
        if (!shopId) {
            resetSlabDiscount(netTotalBeforeSlab);
            return;
        }
        
        $.ajax({
            type: "GET",
            url: "{{ route('calculate.slab.discount') }}",
            data: {
                shop_id: shopId,
                total_amount: baseTotal
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.slab_discount) {
                        var slabDiscount = response.slab_discount;
                        var slabDiscountAmount = parseFloat(slabDiscount.slab_amount) || 0;
                        
                        $('#slab_id').val(slabDiscount.slab_id);
                        $('#slab_percentage').val(slabDiscount.percentage + '%');
                        $('#slab_amount').val(slabDiscount.slab_amount.toFixed(2));
                        
                        var finalTotal = netTotalBeforeSlab - slabDiscountAmount;
                        
                        $('#pending_amount').val(finalTotal.toFixed(2));
                        $('#net_total').val(finalTotal.toFixed(2));
                    } else {
                        resetSlabDiscount(netTotalBeforeSlab);
                    }
                } else {
                    resetSlabDiscount(netTotalBeforeSlab);
                    console.error('Error calculating slab discount:', response.message);
                }
            },
            error: function(xhr, status, error) {
                resetSlabDiscount(netTotalBeforeSlab);
                console.error('AJAX Error:', error);
            }
        });
    }

    function resetSlabDiscount(netTotalBeforeSlab) {
        $('#slab_id').val('');
        $('#slab_percentage').val('0%');
        $('#slab_amount').val('0.00');
        
        $('#pending_amount').val(netTotalBeforeSlab.toFixed(2));
        $('#net_total').val(netTotalBeforeSlab.toFixed(2));
    }

    $(document).on('keyup change', '.data-quantity, .data-rate, #freight_charges, #discount_percent', function() {
        setTimeout(function() {
            calculation();
        }, 300);
    });

    $(document).ready(function(){
        $('.select2').select2();
        
        $(document).on('keyup', '#freight_charges', function(){
            calculation();
        });
        
        $(document).on('keyup', '#discount_percent', function(){
            calculation();
        });
    });

    // Initialize counter based on existing rows + 1
    let counter = {{ count($record->saleOrderData) }} + 1;

    function addMore() {
        $('#appendRow').append(`
        <tr id="removeRow${counter}">
            <input type="hidden" class="data_id" name="sale_order_data_id[]" value="0">
            <td>
                <select style="width:290px !important;" onchange="get_product_price(this);get_flavour(this); get_scheme_product(this); get_total_caton_and_qty();" name="product_id[]" tabindex="-1" class="product_id combobox form-control" required>
                    <option value="">Select a Product</option>
                    @foreach ($products as $product)
                        <option data-carton_size="{{$product->carton_size}}" data-product_price="{{$master->get_product_price($product->id)}}" data-flavour="{{$product->product_flavour}}"data-url="{{ route('sale-order.table-row', $product->id) }}" value="{{ $product->id }}">{{ $product->product_name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select style="width:130px !important;" name="flavour_id[]" id="" class="form-control flavour"></select>
            </td>
            <td>
                <select style="width:130px !important;" name="sale_type[]" onchange="get_rate(this)" id="" class="form-control sale_type"></select>
            </td>
            <td>
                <input style="width:130px !important;" onkeyup="calc(this); get_scheme_product(this); get_total_caton_and_qty();" class="form-control data-quantity" type="number" min="0" name="qty[]" value="0">
            </td>
            <td>
                <input style="width:130px !important;" step="any" onkeyup="calc(this)" class="form-control data-rate" type="number" min="0" name="rate[]" value="0">
            </td>
            <td>
                <input style="width:130px !important;" onkeyup="calc(this)" class="form-control data-discount" type="text" name="data_discount[]" value="0">
            </td>
            <td class="hide">
                <input style="width:130px !important;" class="form-control data-discount-amount" onkeyup="calc(this , true)" type="integer" name="data_discount_amount[]" value="0">
            </td>
            <td class="hide">
                <select style="width:130px !important;" name="scheme_id[]" onchange="get_scheme_amount(this);" id="" class="form-control scheme_product">
                    <option value="">Select</option>
                </select>
            </td>
            <td class="hide">
                <input style="width:130px !important;" readonly class="form-control scheme_amount" step="any" type="number" name="scheme_amount[]" value="0">
            </td>
            <td class="hide">
                <input style="width:130px !important;" readonly class="form-control scheme_qty" step="any" type="number" name="scheme_qty[]" value="0">
            </td>
            <td>
                <select style="width:130px !important;" name="scheme_id_pcs[]" onchange="get_scheme_pcs(this);" id="" class="form-control scheme_product_pcs">
                    <option value="">Select</option>
                </select>
            </td>
            <td>
                <input style="width:130px !important;" readonly class="form-control scheme_pcs" step="any" type="number" name="scheme_pcs[]" value="0">
            </td>
            <td>
                <input style="width:130px !important;" readonly class="form-control sch_d" type="number" name="sch_d[]" value="0">
            </td>
            <td class="hide">
                <input style="width:130px !important;" readonly class="form-control scheme_pcs_total" step="any" type="number" name="scheme_product_pcs_total[]" value="0">
            </td>
            <td class="hide">
                <input style="width:130px !important;" onkeyup="calc(this)" class="form-control data-tax-percent" type="text" name="data_tax_percent[]" value="0">
                <input class="data-tax-amount" type="hidden" name="data_tax_amount[]" value="0">
            </td>
            <td>
                <input style="width:190px !important;" readonly class="form-control data-total" type="text" name="data_total[]" value="0">
            </td>
            <td>
                <button type="button" onclick="removeRow(${counter})" class="btn btn-danger btn-xs"><i class="fa-solid fa-trash"></i></button>
            </td>
        </tr>
        `);
        
        // Initialize select2 for new row
        setTimeout(() => {
            $('#removeRow' + counter + ' select').select2();
        }, 100);
        
        counter++;
        calculation();
    }

    // Updated removeRow function
    function removeRow(params) {
        const row = $('#removeRow' + params);
        
        // Get the sale_order_data_id from the row
        const dataId = row.find('.data_id').val();
        
        if (dataId && dataId !== '0' && dataId !== '') {
            // Add the ID to remove_id hidden field
            const currentRemoveIds = $('.remove_id').val();
            const removeIdsArray = currentRemoveIds ? currentRemoveIds.split(',') : [];
            
            // Add the ID if not already present and it's not empty
            if (!removeIdsArray.includes(dataId)) {
                removeIdsArray.push(dataId);
                $('.remove_id').val(removeIdsArray.join(','));
                console.log('Added to remove_id:', dataId);
            }
        }
        
        // Remove the row from UI
        row.remove();
        
        // Recalculate totals
        calculation();
    }

    function calc(val, discount_amount_type) {
        var $this = $(val);

        // Get values and ensure they are numbers
        var rate = Number($this.closest('tr').find('.data-rate').val()) || 0;
        var qty = Number($this.closest('tr').find('.data-quantity').val()) || 0;
        var sch_d = Number($this.closest('tr').find('.sch_d').val()) || 0;
        var scheme_pcs_total = Number($this.closest('tr').find('.scheme_pcs_total').val()) || 0;
        var scheme_qty = Number($this.closest('tr').find('.scheme_qty').val()) || 1;
        var tax = Number($this.closest('tr').find('.data-tax-percent').val()) || 0;
        var trade_offer_amount = Number($this.closest('tr').find('.trade_offer_amount').val()) || 0;
        var scheme_amount = Number($this.closest('tr').find('.scheme_amount').val()) || 0;

        // Calculate scheme_amount1 safely
        var scheme_amount1 = (scheme_amount * qty) / scheme_qty;

        var applicableSchemes = Math.floor(qty / scheme_pcs_total);
        var totalApplicableSchemes = scheme_qty * applicableSchemes;

        $this.closest('tr').find('.scheme_pcs').val(totalApplicableSchemes);

        var total = qty * rate;
        var tax_amount = (total / 100) * tax;

        $this.closest('tr').find('.data-tax-amount').val(tax_amount);

        var discount_total, discount_percent;

        if (discount_amount_type) {
            discount_total = Number($this.closest('tr').find('.data-discount-amount').val()) || 0;
            discount_percent = (discount_total * 100) / total;
            $this.closest('tr').find('.data-discount').val(discount_percent);
        } else {
            discount_percent = Number($this.closest('tr').find('.data-discount').val()) || 0;
            discount_total = (total / 100) * discount_percent;
            $this.closest('tr').find('.data-discount-amount').val(discount_total);
        }

        console.log("Total:", total, "Discount Total:", discount_total, "Discount Percent:", discount_percent);

        // Calculate final total
        var data_total = total - discount_total + tax_amount - trade_offer_amount - sch_d;
        $this.closest('tr').find('.data-total').val(parseInt(data_total));

        // Call calculation function
        calculation();
    }

    function get_rate(val) {
        sale_type = $(val).closest('tr').find('.sale_type').val();
        rate = $(val).closest('tr').find('.sale_type option:selected').data('rate');

        $(val).closest('tr').find('.data-rate').val(rate);
        calc(val);
    }

    function get_product_price(val) {
        let product_price = $(val).find(':selected').data('product_price');
        console.log(product_price);

        $(val).closest('tr').find('.sale_type').empty();
        product_price.forEach(price => {
            if (price.status === 1) {
                const option = document.createElement('option');
                option.value = price.uom_id;
                option.textContent = price.uom.uom_name;
                $(option).attr('data-rate', price.trade_price);

                $(val).closest('tr').find('.sale_type').append(option);
            }
        });

        get_rate(val);
    }

    function get_flavour(val) {
        let flavours = $(val).find(':selected').data('flavour');

        console.log(flavours);

        $(val).closest('tr').find('.flavour').empty();

        flavours.forEach(flavour => {
            if (flavour.status === 1) {
                const option = document.createElement('option');
                option.value = flavour.id;
                option.textContent = flavour.flavour_name;

                $(val).closest('tr').find('.flavour').append(option);
            }
        });
    }

    function get_scheme_product(val) {
        var row = $(val).closest('tr');
        var qty = row.find('.data-quantity').val();
        var rate = row.find('.data-rate').val();
        var product_id = row.find('.product_id').val();
        var dcdate = $('#dcdate').val();

        // Store previous selections BEFORE clearing dropdowns
        var previousSelectedScheme = row.find('.scheme_product').val() || "";
        var previousSelectedSchemePcs = row.find('.scheme_product_pcs').val() || "";

        $.ajax({
            type: "get",
            url: '{{ route('get_scheme_product') }}',
            data: {
                product_id: product_id,
                qty: qty,
                dcdate: dcdate,
                rate: rate,
            },
            dataType: 'json',
            success: function(data) {
                console.log('AJAX Response:', data);

                let $schemeDropdown = row.find('.scheme_product');
                let $schemePcsDropdown = row.find('.scheme_product_pcs');
                let $schDInput = row.find('.sch_d');
                let $schemepcs = row.find('.scheme_pcs');
                
                let freePcsValue = 0;

                if (data.total_free_pcs !== undefined && data.total_free_pcs !== null) {
                    freePcsValue = parseInt(data.total_free_pcs);
                    if (isNaN(freePcsValue)) freePcsValue = 0;
                }

                console.log('Setting scheme_pcs to:', freePcsValue);

                $schemepcs.prop('readonly', false);
                $schemepcs.val(freePcsValue);
                $schemepcs.prop('readonly', true);

                $schemepcs[0].value = freePcsValue;

                setTimeout(function() {
                    $schemepcs.val(freePcsValue);
                }, 10);

                console.log('Current scheme_pcs value:', $schemepcs.val());

                // Reset old options
                $schemeDropdown.empty().append('<option value="">Select</option>');
                $schemePcsDropdown.empty().append('<option value="">Select</option>');

                let schemeOptions = [];
                let schemePcsOptions = [];

                // Populate scheme_product dropdown
                $.each(data.scheme_product, function(key, value) {
                    let option = `<option value="${value.scheme_id},${value.scheme_data_id}"
                            data-scheme_amount="${value.scheme_amount}"
                            data-qty="${value.qty}">
                            ${value.scheme_name} -- qty ${value.qty}
                        </option>`;
                    schemeOptions.push(option);
                });

                // Populate scheme_product_pcs dropdown
                $.each(data.scheme_product_pcs, function(key, value) {
                    let option = `<option value="${value.scheme_id_pcs},${value.scheme_data_id_pcs}"
                            data-scheme_pcs_get="${value.qty}"
                            data-scheme_pcs="${value.scheme_Pcs}">
                            ${value.scheme_name} -- qty ${value.qty}
                        </option>`;
                    schemePcsOptions.push(option);
                });

                // Append options
                $schemeDropdown.append(schemeOptions.join(''));
                $schemePcsDropdown.append(schemePcsOptions.join(''));

                // Set SCH D value
                if (data.scheme_amount_pcs !== undefined) {
                    $schDInput.val(parseFloat(data.scheme_amount_pcs).toFixed(2));
                } else {
                    $schDInput.val(0);
                }

                // Auto Restore OR Auto Select First
                if (previousSelectedScheme &&
                    $schemeDropdown.find(`option[value="${previousSelectedScheme}"]`).length > 0) {
                    $schemeDropdown.val(previousSelectedScheme);
                } else if ($schemeDropdown.find("option:eq(1)").length > 0) {
                    $schemeDropdown.find("option:eq(1)").prop("selected", true);
                }

                if (previousSelectedSchemePcs &&
                    $schemePcsDropdown.find(`option[value="${previousSelectedSchemePcs}"]`).length > 0) {
                    $schemePcsDropdown.val(previousSelectedSchemePcs);
                } else if ($schemePcsDropdown.find("option:eq(1)").length > 0) {
                    $schemePcsDropdown.find("option:eq(1)").prop("selected", true);
                }

                // Trigger change events
                $schemeDropdown.trigger('change');
                $schemePcsDropdown.trigger('change');

                // Recalculate after setting values
                calc(val);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    }

    function validateSchemeSelection(selectBox) {
        var row = $(selectBox).closest('tr');
        var schemeProduct = row.find('.scheme_product');
        var schemePcs = row.find('.scheme_product_pcs');

        if (schemeProduct.val() && schemePcs.val()) {
            alert("You can select only one scheme at a time!");
            $(selectBox).val('').trigger('change');
            return false;
        }
        return true;
    }

    $(document).on('change', '.scheme_product', function() {
        if (validateSchemeSelection(this)) {
            // Additional logic if needed
        }
    });

    $(document).on('change', '.scheme_product_pcs', function() {
        if (validateSchemeSelection(this)) {
            // Additional logic if needed
        }
    });

    function get_scheme_amount(val) {
        const row = $(val).closest('tr');
        const selectedOption = row.find('.scheme_product option:selected');
        const scheme_amount = selectedOption.attr('data-scheme_amount') || 0;
        const qty = selectedOption.attr('data-qty') || 0;

        console.log('Selected Scheme Amount:', scheme_amount);
        console.log('Selected Qty:', qty);

        row.find('.scheme_amount').val(scheme_amount);
        row.find('.scheme_qty').val(qty);

        calc(val);
    }

    function get_scheme_pcs(val) {
        var scheme_pcs = $(val).closest('tr').find('.scheme_product_pcs option:selected').data('scheme_pcs');
        const row = $(val).closest('tr');
        const scheme_product_pcs = row.find('.scheme_product_pcs option:selected');
        const qty2 = scheme_product_pcs.attr('data-scheme_pcs_get') || 0;

        row.find('.scheme_pcs_total').val(qty2);
        console.log(scheme_pcs);

        $(val).closest('tr').find('.scheme_pcs').val(scheme_pcs ?? 0);

        calc(val);
    }

    function get_total_caton_and_qty() {
        let total_carton = 0;
        let total_qty = 0;
        $('.product_id').each(function(index) {
            carton_size = $(this).find('option:selected').data('carton_size');
            qty = $(this).closest('tr').find('.data-quantity').val();
            sale_type = $(this).closest('tr').find('.sale_type').val();
            if (sale_type == 7) {
                total_carton += parseFloat(qty);
            } else {
                total_qty += parseFloat(qty);
            }
        });
        $('#total_carton').val(total_carton.toFixed(2) ?? 0);
        $('#total_pcs').val(total_qty.toFixed(2) ?? 0);
    }

    // Form submit handler to collect all removed IDs
    $('#subm_rest').on('submit', function(e) {
        console.log('Form submitting...');
        console.log('Current remove_id:', $('.remove_id').val());
        
        // Get all original IDs from page
        const originalIds = [];
        @foreach ($record->saleOrderData as $data)
            originalIds.push('{{ $data->id }}');
        @endforeach
        
        // Get all currently visible IDs
        const currentIds = [];
        $('.data_id').each(function() {
            const id = $(this).val();
            if (id && id !== '0') {
                currentIds.push(id);
            }
        });
        
        // Find IDs that are in original but not in current (removed)
        const removedIds = originalIds.filter(id => !currentIds.includes(id));
        
        if (removedIds.length > 0) {
            const currentRemoveIds = $('.remove_id').val();
            const removeIdsArray = currentRemoveIds ? currentRemoveIds.split(',') : [];
            
            // Add removed IDs to remove_id field
            removedIds.forEach(id => {
                if (!removeIdsArray.includes(id)) {
                    removeIdsArray.push(id);
                }
            });
            
            $('.remove_id').val(removeIdsArray.join(','));
            console.log('Final remove_id:', $('.remove_id').val());
        }
    });
</script>

@endsection
