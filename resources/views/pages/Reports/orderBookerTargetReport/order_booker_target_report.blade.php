<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>
@extends('layouts.master')
@section('title', 'SND || Caregory')
@section('content')


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


<style>

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #6c5ce7 !important; /* apna color */
        border: none !important;
        color: #fff !important;
        font-weight: 500;
        padding: 3px 10px;
        border-radius: 6px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 26px;
        position: absolute;
        top: 1px;
        right: 1px;
        width: 20px;
        display: none !important;
    }

    .select2-container--classic .select2-selection--multiple .select2-selection__choice__remove:before, .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:before {
        content: '' !important;
        background-image: url(data:image/svg+xml,%3Csvg xmlns=!string!viewBox=!string!fill=!string!stroke=!string!stroke-width=!string!stroke-linecap=!string!stroke-linejoin=!string!class=!string!%3E%3Cline x1=!string!y1=!string!x2=!string!y2=!string!%3E%3C/line%3E%3Cline x1=!string!y1=!string!x2=!string!y2=!string!%3E%3C/line%3E%3C/svg%3E);
        background-size: 0.85rem;
        height: 0.85rem;
        width: 0.85rem;
        position: absolute;
        top: 22%;
        left: -4px;
    }

    .select2-container--default.select2-container--open.select2-container--below .select2-selection--single, .select2-container--default.select2-container--open.select2-container--below .select2-selection--multiple {
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
        overflow: auto;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border: solid black 1px;
        outline: 0;
        height: auto !important;
        overflow: auto !important;
    }

    .select2-container--classic .select2-selection--multiple, .select2-container--default .select2-selection--multiple {
        min-height: 100px !important;
        border: 1px solid #d8d6de;
    }

</style>


<script>



$(document).ready(function() {
    $('.select2').select2({
        placeholder: function(){
            return $(this).data('placeholder');
        },
        allowClear: true,
        width: '100%'
    });
});


</script>


    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Order Booker Target Sheet</h4>
                        <button type="button" id="exportBtn" onclick="exportBtnWithFilters('Order Booker Target Sheet')" class="btn btn-success">Export Excel</button>
                    </div>
                    <div class="card-body">
                        <form method="get" action="{{ route('order_booker_target_report') }}" id="list_data" class="form">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>From* </label>
                                        <input type="date"  name="from" class="form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>To* </label>
                                        <input type="date"  name="to" class="form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Distributor Name* </label>
                                        <select required onchange="get_tso_multi()" class="select2" name="distributor_id[]" id="distributor_id" multiple="multiple" data-placeholder="Select Distributors">
                                            @foreach ($master->get_all_distributor_user_wise() as $row)
                                                <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>TSO Name</label>
                                        <select onchange="get_route_by_tso()" class="select2" id="tso_id" name="tso_id[]" multiple="multiple" required data-placeholder="Select TSO Name">
                                            <option value="">All</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Route</label>
                                        <select onchange="get_shop_by_route()" id="route_id" name="route_id" class="select2 form-control form-control-lg"></select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="cust2">
                                        <label class="control-label">Shop</label>
                                        <select class="form-control select2" id="shop_id" name="shop_id" required="">
                                            <option value="">Select a Shop </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Target Type</label>
                                        <select class="form-control select2" id="target_type" name="target_type">
                                            <option value="1">Quantity</option>
                                            <option value="2">Amount</option>
                                            <option value="3">Shop</option>
                                        </select>
                                    </div>
                                </div>
                                {{-- <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Summary</label>
                                        <select class="form-control" id="yes" name="summary">
                                            <option value="0">TARGET</option>
                                            <option value="1">TARGET VS ACHEIVED </option>
                                        </select>
                                    </div>
                                </div> --}}
                                <div class="mt-2">
                                    <div class="print-butts">
                                        <button onclick="get_ajax_data()" type="button"class="btn btn-primary mr-1">Generate</button>
                                        <button type="button" onclick="printTableNew('.printBody')" class="btn btn-primary text-right"> Print </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form method="post" action="{{ route('execution.multi_so_view') }}">
                            @csrf
                            <div class="table-responsive" id="data"></div>
                            <br>
                            <!-- <div class="col-12">
                                    <button type="submit" class="btn btn-primary mr-1 text-right right">View</button>

                                </div> -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Floating Label Form section end -->

@endsection

<script>
    // function printTable(tableId) {
    //     var printContents = document.getElementById(tableId).outerHTML;
    //     var originalContents = document.body.innerHTML;

    //     document.body.innerHTML = printContents;
    //     window.print();

    //     document.body.innerHTML = originalContents;
    // }
</script>


