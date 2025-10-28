@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
@endphp
@extends('layouts.master')
@section('title', 'Add Zone')
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">RECEIVE STOCK (FORM)</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('stockManagement.store') }}" id="subm" class="form">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="distributor_id">Distributor</label>
                                        <select name="distributor_id" id="distributor_id" class="form-control">
                                            <option value="">--Select--</option>
                                            @foreach (MasterFormsHelper::get_all_distributor_user_wise() as $distributor)
                                                <option value="{{ $distributor->id }}">{{ $distributor->distributor_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="stock_received_type">Receiving Type</label>
                                        <select name="stock_received_type" id="stock_received_type" class="form-control">
                                            <option value="1">Stock from principle</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="voucher_date">Date</label>
                                        <input type="date" value="{{ date('Y-m-d') }}" name="voucher_date" id="voucher_date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="stock_type">Stock Type</label>
                                        <select name="stock_type" id="stock_type" class="form-control">

                                            <option value="0">Purchase </option>


                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="remarks">Remarks</label>
                                        <textarea name="remarks" id="remarks" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%;">Products</th>
                                                <th style="width: 20%;">Flavour</th>
                                                <th style="width: 20%;">UOM</th>
                                                <th style="width: 20%;">Quantity</th>
                                                <th style="width: 10%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="productDetail">
                                            <tr>
                                                <td scope="row">
                                                    <select name="product_id[]" required onchange="get_product_price(this); get_flavour(this);" class="form-control select2">
                                                        <option value="">Select Product</option>
                                                        @foreach (MasterFormsHelper::get_all_product() as $product)
                                                            <option data-product_price="{{$master->get_product_price($product->id)}}" data-flavour="{{$product->product_flavour}}" value="{{ $product->id }}">{{ $product->product_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="flavour_id[]"  id="" class="form-control flavour">
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="uom_id[]" id="" class="form-control uom_id">
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="qty[]" id="" value="0"
                                                        step="0.01" required class="form-control">
                                                </td>
                                                <td>
                                                    <button type="button" onclick="addMore()" class="btn btn-primary btn-xs">ADD MORE</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary mr-1">Create Stock</button>
                                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
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
        let counter = 0;
      function addMore() {
    $('#productDetail').append(`
    <tr id="removeRow${++counter}">
        <td scope="row">
            <select name="product_id[]" required onchange="get_product_price(this); get_flavour(this);" class="form-control select2">
                <option value="">Select Product</option>
                @foreach (MasterFormsHelper::get_all_product() as $product)
                    <option data-product_price="{{$master->get_product_price($product->id)}}" data-flavour="{{$product->product_flavour}}" value="{{ $product->id }}">{{ $product->product_name }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select name="flavour_id[]" class="form-control flavour"></select>
        </td>
        <td>
            <select name="uom_id[]" class="form-control uom_id"></select>
        </td>
        <td>
            <input type="number" name="qty[]" value="0" step="0.01" required class="form-control">
        </td>
        <td>
            <button type="button" onClick="removeRow(${counter})" class="btn btn-danger btn-xs">REMOVE</button>
        </td>
    </tr>
    `);

    // Reinitialize Select2 for new elements
    $('.select2').select2();
}


        function removeRow(params) {
            $('#removeRow' +params).remove();
        }




        function get_product_price(val)
        {
            let product_price = $(val).find(':selected').data('product_price');
            console.log(product_price);

            $(val).closest('tr').find('.uom_id').empty();
            product_price.forEach(price => {
                if (price.status === 1) {
                // Create an option element
                const option = document.createElement('option');
                option.value = price.uom_id;   // Set the value attribute to the price ID
                option.textContent = price.uom.uom_name; // Set the visible text
                $(option).attr('data-rate', price.trade_price);

                $(val).closest('tr').find('.uom_id').append(option);
            }
            });


        }

        function get_flavour(val)
        {
            let flavours = $(val).find(':selected').data('flavour');

            console.log(flavours);

            $(val).closest('tr').find('.flavour').empty();

            // Loop through the flavours array
            flavours.forEach(flavour => {
                // Only add the option if status is 1
                if (flavour.status === 1) {
                    // Create an option element
                    const option = document.createElement('option');
                    option.value = flavour.id;   // Set the value attribute to the flavour ID
                    option.textContent = flavour.flavour_name; // Set the visible text

                    $(val).closest('tr').find('.flavour').append(option);
                }
            });

        }


    </script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

 <script>
    $(document).ready(function(){
       
        $('.select2').select2();
    })
 </script> 
@endsection
