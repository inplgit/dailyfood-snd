@php
use App\Models\Channel;
$Channel = Channel::where('status',1)->get();

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
                    <h4 class="card-title">ADD Slab Channel</h4>
                </div>
                <div class="card-body">
                    <form id="subm" method="POST" action="{{ route('slab_category.store') }}" class="form" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-2">
                                <div class="main_head">
                                    <h2>Slab Channel Details</h2>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="row">


                                  
                                <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Slab Name <strong>*</strong></label>
                                            <input name="slab_name" type="text" class="form-control" placeholder="Slab Name"/>
                                        </div>
                                    </div>

                                    <div class="col-md-9 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Slab Description</label>
                                            <textarea  name="description"  class="form-control" placeholder="Slab Description"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-9 col-12">
                                   
                                                    <label class="control-label">Slab Channel</label>
                                                    <select name="channel_id" class=" form-control form-control-lg"  required>
                                                        <option value="">Select</option>
                                                        @foreach ($Channel as $key => $row)
                                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                        @endforeach
                                                    </select>

                                                
                                    </div>
                                   
                                </div>

                                </div>
                            </div>

                            <div class="col-md-12 seprator">
                                <hr>
                            </div>

                            <div class="col-md-2">
                                <div class="main_head">
                               
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="row">

                                    <div class="col-md-12 col-12 append_price_data">
                                            <div class="row remove_data">
                                               
                                              
                                               
                                                <div class="col-md-2 col-2">
                                                    <label class="control-label">From Amount</label>
                                                    <input name="amount[]" required type="number" step="any" placeholder="Amount" class="form-control"/>
                                                </div>
                                                <div class="col-md-2 col-2">
                                                    <label class="control-label">To Amount</label>
                                                    <input name="to_amount[]"  type="number" step="any" placeholder="Amount" class="form-control"/>
                                                </div>
                                                <div class="col-md-2 col-2">
                                                    <label class="control-label">Percentage %</label>
                                                    <input name="percentage[]" required type="number" step="any" placeholder="percentage" class="form-control"/>
                                                </div>
                                                <div class="col-md-2 col-2">
                                                    
                                                    <button style="
                                                            width: max-content;
                                                            margin-top: 30px;
                                                        " class="btn btn-sm btn-primary" type="button" onclick="addMore()">Add More</button>
                                                </div>
                                            </div>
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
    let rowIndex  = 1;
    function addMore() {
        html = `
    <div class="row mt-2 remove_data">
        <div class="col-md-2 col-2">
            <input name="amount[]" required type="number" step="any" placeholder="Amount" class="form-control"/>
        </div>
        <div class="col-md-2 col-2">
            <input name="to_amount[]"  type="number" step="any" placeholder="Amount" class="form-control"/>
        </div>
        <div class="col-md-2 col-2">
            <input name="percentage[]" required type="number" step="any" placeholder="Percentage" class="form-control"/>
        </div>
        <div class="col-md-1 col-1">
            <button class="btn btn-sm btn-danger" type="button" onclick="removeMore(this)">Remove</button>
        </div>
    </div>
`;
$('.append_price_data').append(html);

   
    $('.select2').select2(); // Initialize select2 for new elements
}


    function multipleSelect2()
    {

            $('.select2').select2({
                placeholder: "Select Slab", // Placeholder for select box
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
