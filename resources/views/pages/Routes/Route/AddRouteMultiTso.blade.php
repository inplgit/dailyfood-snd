<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>
@extends('layouts.master')
@section('title', "SND || Category")
@section('content')

<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Add Route</h4>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('AddRouteMultiTso_Store') }}" id="subm" class="form">
                        @csrf
                        <div class="row" id="form-fields-container">
                            <div class="col-md-2">
                                <div class="main_head">
                                    <h2>Route Details</h2>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="row form-group-set">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Route Name</label>
                                            <input type="text" name="routes[0][route_name]" class="form-control" placeholder="Route Name" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Distributor Name</label>
                                            <select onchange="get_tso_route(this)" class="form-control distributor-id" name="routes[0][distributor_id]">
                                                <option value="">select</option>
                                                @foreach ($master->get_all_distributor_user_wise() as $row)
                                                <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">TSO Name</label>
                                            <select class="form-control tso-id" multiple name="routes[0][tso_id][]">
                                                <option value="">select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Days</label>
                                            <select class="form-control day" multiple name="routes[0][day][]">
                                                <option value="">select</option>
                                                @foreach ($master->Days() as $row)
                                                <option value="{{ $row }}">{{ $row }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2 text-right">
                            <button type="button" id="add-more-fields" class="btn btn-success">Add More</button>
                            <button type="submit" class="btn btn-primary mr-1">Create Route</button>
                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
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
  $(document).ready(function () {
    $('.day').select2();
    $('.tso-id').select2();

    let index = 1; // To track dynamic field groups

    // Add more fields
    $('#add-more-fields').on('click', function () {
        const newFields = `
        <div class="row form-group-set mt-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Route Name</label>
                    <input type="text" name="routes[${index}][route_name]" class="form-control" placeholder="Route Name" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Distributor Name</label>
                    <select class="form-control distributor-id" name="routes[${index}][distributor_id]" onchange="get_tso_route(this)">
                        <option value="">select</option>
                        @foreach ($master->get_all_distributor_user_wise() as $row)
                        <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">TSO Name</label>
                    <select class="form-control tso-id" multiple name="routes[${index}][tso_id][]">
                        <option value="">select</option>
                    </select>
                </div>


                 
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Days</label>
                    <select class="form-control day" multiple name="routes[${index}][day][]">
                        <option value="">select</option>
                        @foreach ($master->Days() as $row)
                        <option value="{{ $row }}">{{ $row }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-12 text-right">
                <button type="button" class="btn btn-danger remove-field">Remove</button>
            </div>
        </div>`;

        // Insert new fields after the last form-group-set
        $('.form-group-set').last().after(newFields);

        $('.day').select2(); // Reinitialize select2 for new fields
           $('.tso-id').select2();
        index++;
    });

    // Remove field group
    $('#form-fields-container').on('click', '.remove-field', function () {
        $(this).closest('.form-group-set').remove();
    });
});


function get_tso_route(element) {
        const parentRow = $(element).closest('.form-group-set');
        const distributorId = $(element).val();
        const tsoSelect = parentRow.find('select[name*="[tso_id]"]');

        tsoSelect.html('<option value="">select</option>');

        if (distributorId) {
            $.ajax({
                type: "GET",
                url: '{{ route('route.GetTsoByDistributormulti') }}',
                data: { distributor_id: distributorId },
                dataType: 'json',
                success: function(data) {
                    if (data.tso && data.tso.length > 0) {
                        data.tso.forEach((value) => {
                            tsoSelect.append(new Option(value.name, value.id));
                        });
                    } else {
                        alert('No TSOs found for the selected distributor.');
                    }
                },
                error: function(xhr) {
                    alert('Failed to fetch TSO data.');
                    console.log(xhr.responseText);
                }
            });
        }
    }
   
</script>

@endsection
