<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>
<input type="hidden" name="tso_id" value="{{ $tso_id }}">
<input type="hidden" name="distributor_id" value="{{ $distributor_id }}">
<input type="hidden" name="city" value="{{ $city_id }}">
<input type="hidden" name="month" value="{{ $month }}">
<input type="hidden" name="shop_type_id" value="{{ $shop_type_id }}">

<div class="row">
    <div class="col-md-3 col-12">
        <div class="form-group">
            <label>Total Amount Target</label>
            <input name="total_amount_target" min="0" type="number" value="{{$total_amount_target ?? 0}}" class="form-control" placeholder="Total Amount Target"/>
        </div>
    </div>
    <div class="col-md-3 col-12">
        <div class="form-group">
            <label>Target Filter</label>
            {{-- <input name="product_name[]" readonly type="text" value="{{ $product->product_name }}" class="form-control" placeholder="Product Name"/> --}}
            <select name="target_filter" onchange="targetFilter(this.value)" class="form-control"  id="">
                <option value="1">Individual Product</option>
                <option value="2">All Product</option>
            </select>
        </div>
    </div>
    <div class="col-md-6 col-12">
        <div class="row all_product" style="display: none">
            <div class="col-md-6 col-12 hide">
                <div class="form-group">
                    <label>Select Target Type<strong>*</strong></label>
                    <select name="main_target_type" onchange="mainTargetType(this.value)"  class="form-control"  id="main_target_type">
                        <option value="1">Quantity</option>
                        <option value="2">Amount</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6 col-12 all_quantity" >
                <div class="form-group">
                    <label>Set Target for all Product Pcs<strong>*</strong></label>
                    <input name="all_quantity" onkeyup="setAllTarget(this.value)" type="text" value="" class="form-control" placeholder="Target"/>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="product_list">
    <div class="row">
        <div class="col-md-12">

            @foreach($products as $product)
                @if($product->product_flavour->count() > 0)
                    @foreach($product->product_flavour as $flavour)
                        <div class="row tr">
                        <div class="col-md-2 col-12">
                                <div class="form-group">
                                    <label>Product Code <strong>*</strong></label>
                                    <input  readonly type="text" value="{{ $product->product_code }}" class="form-control"/>
                                  
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label>Product Name <strong>*</strong></label>
                                    <input name="product_name[]" readonly type="text" value="{{ $product->product_name }}" class="form-control"/>
                                    <input name="product_id[]" type="hidden" value="{{ $product->id }}" class="form-control"/>
                                </div>
                            </div>
                                <div class="col-md-3 col-12 hide">
                                    <div class="form-group">
                                        <label>Target Type <strong>*</strong></label>
                                        <select name="target_type[]" onchange="targetType(this)"  class="form-control target_type"  id="">
                                            <option value="1" {{$product->tsoTargetType($tso_id, $month ) == 1 ? 'selected' : ''}}>Quantity</option>
                                            <option value="2" {{$product->tsoTargetType($tso_id, $month ) == 2 ? 'selected' : ''}}>Amount</option>
                                        </select>
                                    </div>
                                </div>
                            <div class="col-md-2 col-12">
                                <div class="form-group">
                                    <label>Flavour <strong>*</strong></label>
                                    <input name="flavour_name[]" readonly type="text" value="{{ $flavour->flavour_name }}" class="form-control"/>
                                    <input name="flavour_id[]" type="hidden" value="{{ $flavour->id }}" class="form-control"/>
                                </div>
                            </div>

                            <div class="col-md-2 col-12 tr_quantity">
                        <div class="form-group">
                            <label>Quantity Pcs <strong>*</strong></label>
                            <input name="quantity[]" min="0" value="{{ $product->tsoTargetNew($tso_id, $month , 1,$flavour->id) }}" type="number" class="form-control quantity"/>
                        </div>
                    </div>
                            <div class="col-md-3 col-12 tr_amount" style="display: none;">
                                <div class="form-group">
                                    <label>Amount <strong>*</strong></label>
                                    <input name="amount[]" min="0" value="{{ $product->tsoTargetNew($tso_id, $month , 2,$flavour->id) }}"  type="number" class="form-control amount"/>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    {{-- Agar product ke koi flavours nahi hain to yeh sirf aik dafa show ho --}}
                    <div class="row tr">
                    <div class="col-md-2 col-12">
                                <div class="form-group">
                                    <label>Product Code <strong>*</strong></label>
                                    <input  readonly type="text" value="{{ $product->product_code }}" class="form-control"/>
                                  
                                </div>
                            </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label>Product Name <strong>*</strong></label>
                                <input name="product_name[]" readonly type="text" value="{{ $product->product_name }}" class="form-control"/>
                                <input name="product_id[]" type="hidden" value="{{ $product->id }}" class="form-control"/>
                            </div>
                        </div>
                        <div class="col-md-3 col-12 hide">
                                    <div class="form-group">
                                        <label>Target Type <strong>*</strong></label>
                                        <select name="target_type[]" onchange="targetType(this)"  class="form-control target_type"  id="">
                                            <option value="1" {{$product->tsoTargetType($tso_id, $month ) == 1 ? 'selected' : ''}}>Quantity</option>
                                            <option value="2" {{$product->tsoTargetType($tso_id, $month ) == 2 ? 'selected' : ''}}>Amount</option>
                                        </select>
                                    </div>
                                </div>

                        <div class="col-md-4 col-12 tr_quantity">
                            <div class="form-group">
                                <label>Quantity Pcs <strong>*</strong></label>
                                <input name="quantity[]" min="0" value="{{ $product->tsoTargetNew($tso_id, $month , 1,$flavour->id) }}" type="number" class="form-control quantity"/>
                            </div>
                        </div>
                        <div class="col-md-3 col-12 tr_amount" style="display: none;">
                                <div class="form-group">
                                    <label>Amount <strong>*</strong></label>
                                    <input name="amount[]" min="0" value="{{ $product->tsoTargetNew($tso_id, $month , 2,$flavour->id) }}"  type="number" class="form-control amount"/>
                                </div>
                            </div>
                    </div>
                @endif
            @endforeach
     


        </div>
        <div class="col-md-6" id="appent_shop_target" style="display: none">
            <div class="row tr">
                <div class="col-md-6 col-12">
                    <h4>Shop Target</h4>
                </div>
                <div class="col-md-6 col-12">
                    <button class="btn btn-sm btn-primary" type="button" onclick="AddMore();">Add Target</button>
                </div>
            </div>
            @foreach ($shop_type as $row1)
            <div class="row tr">
                <div class="col-md-4 col-12">
                    <div class="form-group">
                        <label>Shop Type <strong>*</strong></label>
                        <select name="shop_type_id[]"   class="select2 form-control form-control-lg">
                            <option value="">select</option>
                            @foreach ( $master->get_all_shop_type() as $row )
                            <option value="{{ $row->id }}" {{$row1->shop_type ==  $row->id ? 'selected' : ''}}>{{ $row->shop_type_name }}</option>
                            @endforeach

                        </select>

                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="form-group">
                        <label>Enter Quantity <strong>*</strong></label>
                        <input name="shop_qty[]" value="{{$row1->shop_qty}}" type="number"  class="form-control" placeholder="Enter Shop Quantity"/>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <button class="btn btn-sm btn-danger" type="button" onclick="removeButton(this)">Remove</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('.target_type').each(function(){
            $(this).trigger('change');
        });
    });
        function targetFilter(value){
            if (value == 1) {
                $('.all_product').hide();
                $('.product_list').show();

            }
            else{
                $('.all_product').show();
                $('.product_list').hide();
            }
            mainTargetType($('#main_target_type').val());
        }

        function setAllTarget(value){
            main_target_type = $('#main_target_type').val();
            if (main_target_type == 1) {
                $('.quantity').each(function(){
                    $(this).val(value);
                });
            }
            else{
                $('.amount').each(function(){
                    $(this).val(value);
                });
            }
        }
        function targetType(instance){
            if (instance.value == 1) {
                $(instance).closest('.tr').find('.tr_amount').hide();
                $(instance).closest('.tr').find('.tr_quantity').show();
            }
            else{
                $(instance).closest('.tr').find('.tr_amount').show();
                $(instance).closest('.tr').find('.tr_quantity').hide();
            }
        }

        function mainTargetType(value){
            $('.target_type').each(function(){
                $(this).val(value);
                $(this).trigger('change');
            });
        }

        function AddMore()
        {
            html = `
            <div class="row tr">
                <div class="col-md-4 col-12">
                    <div class="form-group">
                        <label>Shop Type <strong>*</strong></label>
                        <select name="shop_type_id[]"   class="select2 form-control form-control-lg">
                            <option value="">select</option>
                            @foreach ( $master->get_all_shop_type() as $row )
                            <option value="{{ $row->id }}">{{ $row->shop_type_name }}</option>
                            @endforeach

                        </select>

                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="form-group">
                        <label>Enter Quantity <strong>*</strong></label>
                        <input name="shop_qty[]"  type="number"  class="form-control" placeholder="Enter Shop Quantity"/>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <button class="btn btn-sm btn-danger" type="button" onclick="removeButton(this)">Remove</button>
                </div>
            </div>
            `;
            $('#appent_shop_target').append(html);
        }

        function removeButton(instance)
        {
            $(instance).closest('.tr').remove();
        }
</script>
<?php

// \Event::listen('Illuminate\Database\Events\QueryExecuted', function ($query) {
//     var_dump($query->sql);
//     var_dump($query->bindings);
//     var_dump($query->time);
// });
?>
