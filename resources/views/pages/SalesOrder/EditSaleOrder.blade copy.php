<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>

@extends('layouts.master')
@section('title', 'SND || Edit Sales Order')
@section('content')


    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit SALE ORDER</h4>
                    </div>
                    <div class="card-body">
                        <form id="subm" method="POST" action="{{ route('sale.update', $record->id) }}" class="form">
                            @csrf
                            @method('patch')
                            <div class="row">



                                <div class="col-lg-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <section class="panel">

                                                <div class="panel-body">
                                                    <!--Own Text -->

                                                    <div class="form-group">

                                                        <label class="col-lg-4 control-label">Distributor</label><br>

                                                        <select name="distributor_id" class="form-control" id="distid"
                                                            tabindex="-1" aria-hidden="true">
                                                            <option value="" selected="">All</option>
                                                            @foreach ($master->get_all_distributor_user_wise() as $distributor)
                                                                <option {{ $record->distributor_id == $distributor->id ? 'selected' : '' }} value="{{ $distributor->id }}">{{ $distributor->distributor_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="tso2">
                                                        <label class="col-lg-2 control-label">TSO</label>
                                                        <select class="form-control" name="tso_id">
                                                            <option value="">Select a TSO: </option>
                                                            @foreach ( $master->get_all_tso_by_distributor_id($record->distributor_id) as $row )
                                                                <option {{ $record->tso_id == $row->id ? 'selected' : '' }} value="{{ $row->id }}">{{ $row->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <br>

                                                    <div class="cust2"> <label class="col-lg-2 control-label">Shop</label>

                                                        <select class="form-control cid" id="cid" name="shop_id"
                                                            required="">
                                                            <option value="">Select a Shop </option>
                                                            @foreach ($shops as $shop)
                                                                <option {{ $record->shop_id == $shop->id ? 'selected' : '' }} value="{{ $shop->id }}">{{ $shop->company_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div id="subcustomer"></div>

                                                </div>
                                            </section>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <section class="panel">

                                                <div class="panel-body">
                                                    <!--Own Text -->
                                                    Invoice #<br>
                                                    <input readonly name="invoice_no" class=" form-control" tabindex="1"
                                                        type="text" id="onrecord" required="required" value="{{ $record->invoice_no }}">
                                                    <span id="ord"></span>
                                                    <br>

                                                    <div style=" ">
                                                        LPO #<br>
                                                        <input name="lpo_no" class=" form-control" tabindex="10"
                                                            type="number" id="lpo" value="{{ $record->lpo_no }}">
                                                        <br>
                                                    </div>
                                                    Total Amount<br>

                                                    <input id="total_amount" name="total_amount" type="text" tabindex="-1"
                                                        class="total-display form-control" readonly="readonly" value="{{ $record->total_amount }}">

                                                    <!--Own Text End -->
                                                </div>
                                            </section>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <section class="panel">

                                                <div class="panel-body">
                                                    <!--Own Text -->
                                                    DC No.<br>
                                                    <input name="dc_no" class=" form-control" tabindex="10"
                                                        type="number" id="number" value="{{ $record->dc_no }}"/>
                                                    <span id="serial"></span>
                                                    <br>

                                                    DC Date<br>
                                                    <input type="date" name="dc_date" class="form-control" tabindex="10"
                                                        id="" value="{{ $record->dc_date }}"/>
                                                    <br>

                                                    {{-- Total Amount<br>

                                                    <input id="total_amount" name="total_amount" type="text" tabindex="-1"
                                                        class="total-display form-control" readonly="readonly" value="{{ $record->total_amount }}"/> --}}
                                                </div>
                                            </section>
                                        </div>
                                    </div>
                                </div>
                                <div style=" width:100%; clear:both;">
                                    <div id="psearch" class="psearch"></div>
                                    <div class="cartcustom-overlay">
                                        <table width="100%" cellpadding="5" class="table table-bordered table-striped "
                                            id="acart">
                                            <thead>
                                                <tr>
                                                    <th width="2%">S.No</th>
                                                    <th width="20%">
                                                        <div>Product Name</div>
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
                                                    <th>
                                                        <div>Disc Amount</div>
                                                    </th>

                                                    <th class="hide">
                                                        <div>Tax (%)</div>
                                                    </th>
                                                    <th>
                                                        <div>Total </div>
                                                    </th>

                                                    <th>
                                                        <div>Scheme Product </div>
                                                    </th>

                                                    <th>
                                                        <div>Offer</div>
                                                    </th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <input type="hidden" class="remove_id" name="remove_id" value="">
                                            <tbody id="appendRow">
                                                @foreach ($record->saleOrderData as $key => $data)

                                                    <tr  id="removeRow{{ $key }}">
                                                        <td>
                                                            {{$key+1}}
                                                        </td>
                                                        <input type="hidden" class="data_id" name="sale_order_data_id[]" value="{{ $data->id }}">
                                                        <td>
                                                            <select onchange="get_rate(this)" name="product_id[]" tabindex="-1" id="searchbox"
                                                            required class="combobox form-control" aria-hidden="true">
                                                            <option value="">Select a Product</option>
                                                            @foreach ($products as $product)
                                                                <option data-rate="{{ $product->sales_price }}" {{ $data->product_id == $product->id ? 'selected' : ''}} data-url="{{ route('sale-order.table-row', $product->id) }}" value="{{ $product->id }}">{{ $product->product_name }}</option>
                                                            @endforeach
                                                        </select>

                                                        </td>
                                                        <td>
                                                            <input onkeyup="calc(this)" onblur="calc(this)" class="form-control data-quantity" type="number" min="0" name="qty[]" value="{{ (int)$data->qty ?? 0 }}">
                                                        </td>
                                                        <td>
                                                            <input step="any" onkeyup="calc(this)" onblur="calc(this)" class="form-control data-rate" type="number" min="0" name="rate[]" value="{{ $data->rate ?? 0 }}">
                                                        </td>
                                                        <td>
                                                            <input onkeyup="calc(this)" onblur="calc(this)" class="form-control data-discount" type="text" name="data_discount[]" value="{{ $data->discount ?? 0 }}">
                                                            {{-- <input class="data-discount-amount" type="hidden" name="data_discount_amount[]" value="{{ $data->discount_amount ?? 0 }}"> --}}
                                                        </td>
                                                        <td>
                                                            <input class="form-control data-discount-amount" onkeyup="calc(this , true)"   type="integer" name="data_discount_amount[]" value="0">
                                                        </td>
                                                        <td class="hide">
                                                            <input onkeyup="calc(this)" onblur="calc(this)" readonly class="form-control data-tax-percent" type="text" name="data_tax_percent[]" value="{{ $data->tax_percent ?? 0 }}">
                                                            <input class="data-tax-amount" type="hidden" name="data_tax_amount[]" value="{{ $data->tax_amount ?? 0 }}">
                                                        </td>

                                                        <td class="">
                                                            <input readonly class="form-control data-total" type="text" name="data_total[]" value="{{ $data->total ?? 0 }}">
                                                        </td>


                                                        <td class="">
                                                            <select  name="shceme_product_id[]"  id="searchbox"
                                                                     class="combobox form-control" aria-hidden="true">
                                                                <option value="">Select a Product</option>
                                                                @foreach ($shceme_products as $product)
                                                                    <option data-rate="" {{ $data->sheme_product_id == $product->id ? 'selected' : ''}} data-url="{{ route('sale-order.table-row', $product->id) }}" value="{{ $product->id }}">{{ $product->product_name }}</option>
                                                                @endforeach
                                                            </select>

                                                        </td>

                                                        <td>
                                                            <input  class="form-control" type="text" name="offer[]" value="{{ $data->offer_qty }}">

                                                        </td>


                                                        <td>
                                                            @if ($key == 0)
                                                            <button type="button" class="btn btn-primary" id="add-more" onclick="addMore()">Add More</button>
                                                            @else
                                                            <button type="button" onclick="removeRow({{ $key }},this)" class="btn btn-danger btn-xs">-</button>
                                                            @endif
                                                        </td>



                                                    </tr>


                                                @endforeach
                                            </tbody>
                                        </table>

                                    </div>
                                    <br>
                                    <br>
                                    <br>
                                </div>



                                <div class="col-lg-3">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <section class="panel">
                                                <div class="panel-body">
                                                    <!--Own Text -->





                                                    Discount (%) <input id="discount_percent" name="discount_percent" type="text"
                                                        class="form-control" tabindex="15" accesskey="d"
                                                        value="{{ $record->discount_percent ?? 0 }}"><br>
                                                    <input id="discount_amount" name="discount_amount" type="hidden"
                                                        class="form-control"
                                                        value="{{ $record->discount_amount }}"><br>

                                                    <div class="invoicetax">
                                                        Tax Applied <input id="tax_applied" name="tax_applied" type="text" id="tax"
                                                            value="0" class="form-control" readonly="" {{ $record->tax_applied }}><br></div>
                                                    Freight Charges <input id="freight_charges" name="freight_charges" accesskey="f" type="number" min="0"
                                                        id="shipping" value="{{ $record->freight_charges ?? 0 }}" tabindex="15"
                                                        class="form-control">
                                                    <br>


                                                    Notes
                                                    <textarea name="notes" class="form-control">{{ $record->notes ?? '' }}</textarea>
                                                    <br>



                                                    Transport Details
                                                    <textarea name="transport_details" class="form-control">{{ $record->transport_details ?? '' }}</textarea>




                                                    <!--Own Text End -->
                                                </div>
                                            </section>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-3">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <section class="panel">
                                                <div class="panel-body">

                                                    Execution <select name="excecution" class="form-control">
                                                        <option {{ $record->execution ? 'selected' : '' }} value="1">Yes</option>
                                                        <option {{ !$record->execution ? 'selected' : '' }} value="0" selected="">No</option>
                                                    </select>

                                                    <br>

                                                    Payment <select name="payment_type" class="form-control">
                                                        <option {{ $record->payment_type == 'credit' ? 'selected' : '' }} value="credit" selected="">Credit</option>
                                                        <option {{ $record->payment_type == 'cash' ? 'selected' : '' }} value="cash">Cash</option>
                                                    </select>

                                                    <br>


                                                    Execution Date <input name="excecution_date" tabindex="10" type="date"
                                                        id="date" class="form-control" value="{{ $record->excecution_date ?? '' }}">

                                                    <br>

                                                    <!--Own Text End -->
                                                </div>
                                            </section>
                                        </div>
                                    </div>
                                </div>




                                <div class="col-lg-3">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <section class="panel">
                                                <div class="panel-body">
                                                    <!--Own Text -->
                                                    Total Carton <input type="text" name="total_carton" id="total_carton"
                                                        value="{{ $record->total_carton ?? 0 }}" class="form-control"
                                                        ><br>

                                                    Total Pcs <input name="total_pcs" type="text" id="total_pcs"
                                                        value="{{ $record->total_pcs ?? 0 }}" class="form-control"
                                                        ><br>
                                                    Products Subtotal <input id="products_subtotal" name="products_subtotal" type="text"
                                                        class="total-box form-control" id="products_subtotal" value="{{ $record->products_subtotal ?? 0 }}">



                                                    <br> Cost Center <select name="cost_center" id="combobox"
                                                        class="teacher form-control">
                                                        <option {{ $record->cost_center == 'embroidery' ? 'selected' : '' }} value="embroidery">Embroidery</option>
                                                        <option {{ $record->cost_center == 'knitting' ? 'selected' : '' }} value="knitting">Knitting</option>
                                                        <option {{ $record->cost_center == 'factory_expenses' ? 'selected' : '' }} value="factory_expenses">Factory Expenses</option>
                                                    </select>

                                                    <br>

                                                    <!--Own Text End -->
                                                </div>
                                            </section>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-3">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <section class="panel">
                                                <div class="panel-body">
                                                    <!--Own Text -->


                                                    Pending Amount <input id="pending_amount" name="pending_amount" class="form-control"
                                                        type="text" id="pending_amount" readonly="" value="{{ $record->pending_amount ?? 0 }}">
                                                    <br>

                                                    <div id="oldr">Old Receivable <input id="old_receivable" name="old_receivable"
                                                            type="text" class="form-control" id="old_receivable"
                                                            readonly="" value="0">
                                                        <br>
                                                    </div>




                                                    <!--Own Text End -->
                                                </div>
                                            </section>
                                        </div>
                                    </div>
                                </div>


                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tbody>
                                        <tr>
                                            <td align="left">



                                                <h3>Total</h3> <input id="net_total" name="total" type="text"
                                                    class="change form-control" id="total" readonly="readonly"
                                                    value="{{ $record->total_amount }}">
                                                <br>
                                                <br>

                            </div>
                        </form>

                        <input name="submit2" type="submit" id="submit2" accesskey="o" tabindex="15"
                            class="btn btn-warning" value="Create Sales Order">
                        <input name="Reset" type="reset" id="Reset" class="btn btn-info">

                        </td>
                        </tr>
                        </tbody>
                        </table>



                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Floating Label Form section end -->

@endsection
@section('script')
    <script>
        function calculation() {
            var $total = 0;
            $( '.data-total' ).each(function( index ) {
                // console.log(this);
                $total += parseFloat(this.value)
            });
            $('#products_subtotal').val($total);
            var freight_charges = $('#freight_charges').val();
            var discount_percent = $('#discount_percent').val();
            var discount_amount = ( parseFloat($total) / 100 ) * parseFloat(discount_percent)
            var $net_total = $total + parseFloat(freight_charges) - parseFloat(discount_amount);
            $('#total_amount').val($net_total);
            $('#discount_amount').val(discount_amount);
            $('#pending_amount').val($net_total);
            $('#net_total').val($net_total);
            console.log(discount_amount);
        }
        $(document).ready(function(){
            // $('#searchbox').on('change',  function(){
            //     console.log($(this).find(':selected').data('url'))
            //     var product_id = $(this).val()
            //     $.ajax({
            //     type: "get",
            //     url: $(this).find(':selected').data('url'),
            //     data: {}, // serializes the form's elements.
            //     cache: false,
            //     success: function(data)
            //     {
            //         $('#appendRow').append(data);

            //     }
            // });
            // });
            calculation();

            $(document).on('keyup', '#freight_charges', function(){
                calculation();
            });
            $(document).on('keyup', '#discount_percent', function(){
                calculation();
            });
            $(document).on('change', '#distributor_id', function(){
                var options = '';
                var id = $(this).val()
                $.ajax({
                    type: "get",
                    url: "{{ route('tso.by.distributor') }}",
                    data: {id:id},
                    cache: false,
                    success: function(data)
                    {
                        data.res.forEach(element => {
                            options += `<option value="${element.id}">${element.name}</option>`
                        });
                        $('#tso_id').html(options);

                    }
                });
            });
            $(document).on('change', '#tso_id', function(){
                var options = '';
                var id = $(this).val()
                $.ajax({
                    type: "get",
                    url: "{{ route('shop.by.tso') }}",
                    data: {id:id},
                    cache: false,
                    success: function(data)
                    {
                        data.res.forEach(element => {
                            options += `<option value="${element.id}">${element.company_name}</option>`
                        });
                        $('#shop_id').html(options);

                    }
                });
            });

        });
        let counter = {{ count($record->saleOrderData) }};
        function addMore() {
            $('#appendRow').append(`
            <tr id="removeRow${counter}">
                <td>
                    ${counter+1}
                </td>
                <td>
                    <select onchange="get_rate(this)" name="product_id[]" tabindex="-1" id="searchbox"
                        required class="combobox form-control" aria-hidden="true">
                        <option value="">Select a Product</option>
                        @foreach ($products as $product)
                            <option data-rate="{{ $product->sales_price }}" data-url="{{ route('sale-order.table-row', $product->id) }}" value="{{ $product->id }}">{{ $product->product_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input onkeyup="calc(this)" onblur="calc(this)" class="form-control data-quantity" type="number" min="0" name="qty[]" value="0">
                </td>
                <td>
                    <input step="any" onkeyup="calc(this)" onblur="calc(this)" class="form-control data-rate" type="number" min="0" name="rate[]" value="0">
                </td>
                <td>
                    <input onkeyup="calc(this)" onblur="calc(this)" class="form-control data-discount" type="text" name="data_discount[]" value="0">
                </td>
                <td>
                    <input class="form-control data-discount-amount" onkeyup="calc(this , true)"   type="integer" name="data_discount_amount[]" value="0">
                </td>

                <td class="hide">
                    <input onkeyup="calc(this)" onblur="calc(this)" class="form-control data-tax-percent" type="text" name="tax_percent[]" value="0">
                    <input class="data-tax-amount" type="hidden" name="tax_amount[]" value="0">
                </td>
                <td>
                    <input readonly class="form-control data-total" type="text" name="data_total[]" value="0">
                </td>


            <td>
                <select  name="shceme_product_id[]" tabindex="-1" id="searchbox"
                         class="combobox form-control" aria-hidden="true">
                        <option value="">Select a Product</option>
                        @foreach ($shceme_products as $product)
            <option data-rate="" data-url="" value="{{ $product->id }}">{{ $product->product_name }}</option>
                        @endforeach
            </select>
            </td>

                  <td>
                    <input  class="form-control" type="text" name="offer[]" value="0">
                </td>
        <td>
            <button type="button" onclick="removeRow(${counter})" class="btn btn-danger btn-xs">-</button>
                </td>
            </tr>
            `)
            ++counter;
        }

        var removed_id = [];
        function removeRow(params,val) {
            var $this = $(val)
            $('#removeRow' +params).remove();
            var id = $this.closest('tr').find('.data_id').val();
            removed_id.push(id);
            $('.remove_id').val(removed_id);
            calculation();
        }


        function calc(val , discount_amount_type)
        {

            $this = $(val);


                var rate = $this.closest('tr').find('.data-rate').val();
                var qty = $this.closest('tr').find('.data-quantity').val();
                var tax = $this.closest('tr').find('.data-tax-percent').val();
                var total = parseFloat(qty) * parseFloat(rate);
                var tax_amount = (parseFloat(total)/100) * parseFloat(tax);
                $this.closest('tr').find('.data-tax-amount').val(tax_amount);
                if (discount_amount_type) {
                    var discount_amount =$this.closest('tr').find('.data-discount-amount').val();
                    var discount_total = (parseFloat(discount_amount)*100) / parseFloat(total) ;
                    $this.closest('tr').find('.data-discount').val(discount_total);
                }
                else{
                    var discount_percent =$this.closest('tr').find('.data-discount').val();
                    var discount_total = (parseFloat(total)/100) * parseFloat(discount_percent);
                    $this.closest('tr').find('.data-discount-amount').val(discount_total);
                }
                console.log(total, discount_total, discount_percent);
                var data_total = $this.closest('tr').find('.data-total').val(parseFloat(total) - parseFloat(discount_total) + parseFloat(tax_amount));
                calculation();
        }

       function get_rate(val)
       {

        let rate = $(val).find(':selected').data('rate');
        $(val).closest('tr').find('.data-rate').val(rate);
        calc(val);



       }
    </script>
@endsection
