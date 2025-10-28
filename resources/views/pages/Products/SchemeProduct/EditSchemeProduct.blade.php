@php
use App\Models\Product;
$product_data = Product::Status()->get();
// $uom_data = $product_data->get_all_uom();
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
@endphp
@extends('layouts.master')
@section('title', "SND || Caregory")
@section('content')

<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Scheme PRODUCT</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('scheme_product.update',$scheme_product->id) }}" class="form" >
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-md-2">
                                <div class="main_head">
                                    <h2>Scheme Details</h2>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="row">


                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Scheme Name <strong>*</strong></label>
                                            <input name="scheme_name" type="text" class="form-control" value="{{$scheme_product->scheme_name}}" placeholder="Scheme Name"/>
                                        </div>
                                    </div>

                                    <div class="col-md-9 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Scheme Description</label>
                                            <textarea  name="description"  class="form-control" value="{{$scheme_product->description}}" placeholder="Scheme Description"></textarea>
                                        </div>
                                    </div>


				 <div class="col-md-3 col-12">
 		                                   <div class="form-group">
                                        <label class="control-label">Start Date <strong>*</strong></label>
                                        <input name="start_date" type="date" value="{{$scheme_product->start_date}}" class="form-control" required />
                                    </div>
                                </div>

                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label class="control-label">End Date <strong>*</strong></label>
                                        <input name="end_date" type="date" value="{{$scheme_product->end_date}}"class="form-control" required />
                                    </div>
                                </div>

                                </div>
                            </div>

                            <div class="col-md-12 seprator">
                                <hr>
                            </div>

                            <div class="col-md-2">
                                <div class="main_head">
                                    <h2>Scheme Product</h2>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="row">

                                    <div class="col-md-12 col-12 append_price_data">
                                            <div class="row ">
                                                <div class="col-md-3 col-3">
                                                    <label class="control-label">Product</label>
                                                </div>
                                                <div class="col-md-3 col-3">
                                                    <label class="control-label">Sale Type</label>
                                                </div>
                                                <div class="col-md-3 col-3">
                                                    <label class="control-label">Qty</label>
                                                </div>
                                                <div class="col-md-3 col-3">
                                                    <label class="control-label">Scheme Amount</label>
                                                </div>
                                            </div>
                                            @php
                                                $rowIndex = 0;
                                            @endphp
                                            @foreach ($scheme_product->SchemeProductData as $data)
                                                {{-- @php
                                                    $product_ids = explode(',',$data->product_id) ;
                                                @endphp --}}
                                                <div class="row mt-2 remove_data">
                                                    <div class="col-md-3 col-3">
                                                        <select name="product_id[{{$rowIndex}}]" onchange="get_product_price(this);" class="select2 form-control form-control-lg" required>
                                                            <option value="">Select</option>
                                                            {{-- <option value="select_all">Select All</option> --}}
                                                            @foreach ($product_data as $key => $row )
                                                            <option value="{{ $row->id }}" data-product_price="{{$master->get_product_price($row->id)}}" {{$row->id == $data->product_id ? 'selected' : ''}}>{{ $row->product_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2 col-2">
                                                        <select name="sale_type[{{$rowIndex}}]" id="" class="form-control sale_type">
                                                            @foreach ($data->product->product_price as $product_price)
                                                                <option value="{{$product_price->uom_id}}" {{$product_price->uom_id == $data->sale_type ? 'selected' : ''}} >{{$product_price->uom->uom_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2 col-2">
                                                        <input name="qty[{{$rowIndex}}]" required type="number" value="{{$data->qty}}" step="any" placeholder="QTY" class="form-control"/>
                                                    </div>

                                                    <div class="col-md-3 col-3">
                                                        <input name="scheme_amount[{{$rowIndex}}]" required type="number" value="{{$data->scheme_amount}}" step="any" placeholder="Scheme Amount" class="form-control"/>
                                                    </div>
                                                    <div class="col-md-1 col-1">
                                                        @if ($rowIndex == 0)
                                                            <button class="btn btn-sm btn-primary" type="button" onclick="addMore()">Add More</button>
                                                        @else
                                                        <button class="btn btn-sm btn-danger" type="button" onclick="removeMore(this)">Remove</button>
                                                        @endif
                                                    </div>
                                                </div>
                                                @php
                                                    $rowIndex++;
                                                @endphp
                                            @endforeach

                                    </div>


                                </div>
                            </div>

                            <div class="col-md-12 text-right">
                                <div class="button_create">
                                    <button type="submit" class="btn btn-primary mr-1">Save</button>
                                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
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
    $(document).ready(function() {
        multipleSelect2();
    });

    function removeMore(instance)
    {
        $(instance).closest('.remove_data').remove();
    }
    let rowIndex  = {{$rowIndex}};
    function addMore()
    {
        html = `
         <div class="row mt-2 remove_data">
                                                <div class="col-md-3 col-3">
                                                    <select name="product_id[${rowIndex}]" onchange="get_product_price(this);" class="select2 form-control form-control-lg" required>
                                                        <option value="">Select</option>
                                                        @foreach ($product_data as $key => $row )
                                                        <option value="{{ $row->id }}" data-product_price="{{$master->get_product_price($row->id)}}">{{ $row->product_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                  <div class="col-md-2 col-2">
                                                    <select name="sale_type[${rowIndex}]" id="" class="form-control sale_type">
                                                    </select>
                                                </div>


                                                <div class="col-md-2 col-2">
                                                    <input name="qty[${rowIndex}]" required type="number" step="any" placeholder="QTY" class="form-control"/>
                                                </div>

                                                <div class="col-md-3 col-3">
                                                    <input name="scheme_amount[${rowIndex}]" required type="number" step="any" placeholder="Scheme Amount" class="form-control"/>
                                                </div>
                                                <div class="col-md-1 col-1">
                                                    <button class="btn btn-sm btn-danger" type="button" onclick="removeMore(this)">Remove</button>
                                                </div>
                                            </div>
        `;
        $('.append_price_data').append(html);
        // $('.select2').select2();
        multipleSelect2();
        rowIndex++;
    }

    function multipleSelect2()
    {

            $('.select2').select2({
                placeholder: "Select products", // Placeholder for select box
                // closeOnSelect: false // Prevents closing dropdown when selecting multiple items
            });




            // // Handle the "Select All" functionality
            // $('.select2').on('select2:select', function(e) {
            // if (e.params.data.id === "select_all") {
            //     // Select all options (excluding "Select All")
            //     $(this).find('option:not([value="select_all"])').prop('selected', true);
            //     $(this).trigger('change'); // Trigger change event to reflect the selection
            // }
            // });

            // // Handle "Deselect All" functionality (unselecting when "Select All" is deselected)
            // $('.select2').on('select2:unselect', function(e) {
            //     if (e.params.data.id === "select_all") {
            //         // Unselect all options
            //         $(this).find('option').prop('selected', false);
            //         $(this).trigger('change'); // Trigger change event to reflect the unselection
            //     }
            // });


    }



    function get_product_price(val)
        {
            let product_price = $(val).find(':selected').data('product_price');
            console.log(product_price);

            $(val).closest('.remove_data').find('.sale_type').empty();
            product_price.forEach(price => {
                if (price.status === 1) {
                // Create an option element
                const option = document.createElement('option');
                option.value = price.uom_id;   // Set the value attribute to the price ID
                option.textContent = price.uom.uom_name; // Set the visible text
                $(option).attr('data-rate', price.trade_price);

                $(val).closest('.remove_data').find('.sale_type').append(option);
            }
            });

        }

</script>
@endsection
