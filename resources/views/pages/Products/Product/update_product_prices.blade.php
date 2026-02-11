@php
    use App\Models\Product;
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();


    $product_data = new Product();
    $uom_data = $product_data->get_all_uom();
@endphp
@extends('layouts.master')
@section('title', 'Add Zone')
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Update Product Prices </h4>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('product.update_product_prices_store') }}" id="subm" class="form">
                            @csrf
                            <div class="row">
                                
                                <div class="col-md-12">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th style="width: 15%;">Products</th>
                                                 <th style="width: 15%;">Start Date</th>
                                                <th style="width: 15%;">UOM</th>
                                                <th style="width: 15%;">Retail Price</th>
                                                <th style="width: 15%;">Trade Price</th>
                                                <th style="width: 15%;">Pcs Per Carton</th>
                                                <th style="width: 10%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="productDetail">
                                            <tr>
                                                <td scope="row">
                                                    <select name="product_id[]" required onchange="get_product_price(this);" class="form-control product_id">
                                                        <option value="">Select Product</option>
                                                        @foreach (MasterFormsHelper::get_all_product_with_prices() as $product)
                                                            <option data-product_price="{{$master->get_product_price($product->id)}}" value="{{ $product->id }}">{{ $product->product_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="date" value="{{date('Y-m-d')}}" name="start_date[]" class="form-control form-control-lg"/>
                                                </td>
                                                <td>
                                                   <select name="uom_id[]" class="select2 form-control form-control-lg uom_id">
                                                        <option value="">Select</option>
                                                        @foreach ($uom_data as $key => $row )
                                                        <option value="{{ $row->id }}">{{ $row->uom_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input name="retail_price[]" required type="number" step="any" placeholder="Retail Price" class="form-control retail_price"/>
                                                </td>
                                                <td>
                                                    <input name="trade_price[]" required type="number" step="any" placeholder="Trade Price" class="form-control trade_price"/>
                                                </td>
                                                <td>
                                                     <input name="pcs_per_carton[]" required type="number" step="any" placeholder="Pieces Per Carton" class="form-control pcs_per_carton"/>
                                                </td>
                                                <td>
                                                    <button type="button" onclick="addMore()" class="btn btn-primary btn-xs">ADD MORE</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary mr-1">Update</button>
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

        $(document).ready(function() {
           $('.product_id').select2();
       });

        let counter = 0;
        function addMore() {
            $('#productDetail').append(`
            <tr id="removeRow${++counter}">
                <td scope="row">
                    <select name="product_id[]" required onchange="get_product_price(this);"  class="form-control product_id">
                        <option value="">Select Product</option>
                        @foreach (MasterFormsHelper::get_all_product_with_prices() as $product)
                            <option  data-product_price="{{$master->get_product_price($product->id)}}" data-flavour="{{$product->product_flavour}}"  value="{{ $product->id }}">{{ $product->product_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="date" value="{{date('Y-m-d')}}" name="start_date[]" class="form-control form-control-lg"/>
                </td>
                 <td>
                    <select name="uom_id[]" class="select2 form-control form-control-lg uom_id">
                        <option value="">Select</option>
                        @foreach ($uom_data as $key => $row )
                        <option value="{{ $row->id }}">{{ $row->uom_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                     <input name="retail_price[]" required type="number" step="any" placeholder="Retail Price" class="form-control retail_price"/>
                </td>
                <td>
                    <input name="trade_price[]" required type="number" step="any" placeholder="Trade Price" class="form-control trade_price"/>
                </td>
                <td>
                    <input name="pcs_per_carton[]" required type="number" step="any" placeholder="Pieces Per Carton" class="form-control pcs_per_carton"/>
                </td>
                <td>
                    <button type="button" onClick="removeRow(${counter})" class="btn btn-danger btn-xs">REMOVE</button>
                </td>
            </tr>
            `)
            $('.product_id').select2();
        }
        function removeRow(params) {
            $('#removeRow' +params).remove();
        }


        let productPricesCache = {};

        function get_product_price(val) {
            let product_price = $(val).find(':selected').data('product_price');
            let row = $(val).closest('tr');
            let seenUoms = new Set(); // 👈 for tracking unique uom_id

            productPricesCache[row.attr('id')] = product_price;

            row.find('.uom_id').empty().append(`<option value="">Select</option>`);

            product_price.forEach(price => {
                if (!seenUoms.has(price.uom_id)) {
                    seenUoms.add(price.uom_id); // 👈 add to set

                    const option = document.createElement('option');
                    option.value = price.uom_id;
                    option.textContent = price.uom.uom_name;
                    row.find('.uom_id').append(option);
                }
            });

            // Set default price for first unique entry
            if (product_price.length > 0) {
                row.find('.retail_price').val(product_price[0].retail_price);
                row.find('.trade_price').val(product_price[0].trade_price);
                row.find('.pcs_per_carton').val(product_price[0].pcs_per_carton);
                row.find('.uom_id').val(product_price[0].uom_id);
            }
        }

        $(document).on('change', '.uom_id', function () {
            let row = $(this).closest('tr');
            let uom_id = $(this).val();
            let rowId = row.attr('id');
            let productPrices = productPricesCache[rowId];

            if (productPrices) {
                let selectedPrice = productPrices.find(price => price.uom_id == uom_id);
                if (selectedPrice) {
                    row.find('.retail_price').val(selectedPrice.retail_price);
                    row.find('.trade_price').val(selectedPrice.trade_price);
                    row.find('.pcs_per_carton').val(selectedPrice.pcs_per_carton);
                }
            }
        });


    </script>
@endsection
