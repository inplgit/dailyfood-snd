@php
use App\Models\Channel;
$Channel = Channel::where('status',1)->get();
@endphp

@extends('layouts.master')

@section('title', "SND || Edit Slab Category")

@section('content')

<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Slab Category</h4>
                </div>
                <div class="card-body">
                    <form id="subm" method="POST" action="{{ route('slab_category.update', $slabCategory->id) }}" class="form" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-2">
                                <div class="main_head">
                                    <h2>Slab Category Details</h2>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="row">

                                    <!-- Slab Name Field -->
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Slab Name <strong>*</strong></label>
                                            <input name="slab_name" type="text" class="form-control" value="{{ old('slab_name', $slabCategory->slab_name) }}" placeholder="Slab Name"/>
                                        </div>
                                    </div>

                                    <!-- Slab Description Field -->
                                    <div class="col-md-9 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Slab Description</label>
                                            <textarea name="description" class="form-control" placeholder="Slab Description">{{ old('description', $slabCategory->description) }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Slab Category Selection -->
                                    <div class="col-md-9 col-12">
    <label class="control-label">Slab category</label>
    <select name="channel_id" class=" form-control form-control-lg"  required>
        <option value="">Select</option>
        @foreach ($Channel as $key => $row)
            <option value="{{ $row->id }}" 
                {{ $row->id == $slabCategory->channel_id ? 'selected' : '' }}>
                {{ $row->name }}
            </option>
        @endforeach
    </select>
</div>


                                </div>
                            </div>

                            <div class="col-md-12 seprator">
                                <hr>
                            </div>

                            <div class="col-md-2">
                                <div class="main_head">
                                    <h2>Slab Category</h2>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="row">

                                    <!-- Existing Price Data -->
                                    <div class="col-md-12 col-12 append_price_data">
                                        @foreach ($slabCategory->SlabCategoryDetail as $category)
                                            <div class="row remove_data">
                                                <div class="col-md-2 col-2">
                                                    <label class="control-label">From Amount</label>
                                                    <input name="amount[]" required type="number" step="any" value="{{ old('amount[]', $category->amount) }}" placeholder="Amount" class="form-control"/>
                                                </div>
                                                <div class="col-md-2 col-2">
                                                    <label class="control-label">To Amount</label>
                                                    <input name="to_amount[]"  type="number" step="any" value="{{ old('to_amount[]', $category->to_amount) }}" placeholder="Amount" class="form-control"/>
                                                </div>
                                                <div class="col-md-2 col-2">
                                                    <label class="control-label">Percentage</label>
                                                    <input name="percentage[]" required type="number" step="any" value="{{ old('percentage[]', $category->percentage) }}" placeholder="Percentage" class="form-control"/>
                                                </div>
                                                <div class="col-md-2 col-2">
                                                    <button style="margin-top:30px;" class="btn btn-sm btn-danger" type="button" onclick="removeMore(this)">Remove</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="col-md-12 col-12 text-left">
                                       <button type="button" style="margin-top:5px;" class="btn btn-sm btn-primary" onclick="addMore()">Add More</button>
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

@endsection

@section('script')
<script>
    $(document).ready(function() {
        multipleSelect2();
    });

    function removeMore(instance) {
        $(instance).closest('.remove_data').remove();
    }

    let rowIndex = 1;

    function addMore() {
        html = `
            <div class="row mt-2 remove_data">
                <div class="col-md-2 col-2">
                    <input name="amount[]" required type="number" step="any" placeholder="Amount" class="form-control"/>
                </div>
                 <div class="col-md-2 col-2">
                    <input name="to_amount[]" type="number" step="any" placeholder="Amount" class="form-control"/>
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

    function multipleSelect2() {
        $('.select2').select2({
            placeholder: "Select Slab",
        });
    }
</script>
@endsection
