<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>


@extends('layouts.master')
@section('title', 'Sale Order')
@section('content')

<div class="card">

            <form id="list_data" method="get" action="{{ route('sale.index') }}">
                <div class="form-row">
                <div class="col-md-2 mb-2">
            <label class="control-label" for="start-date">From</label>
            <input type="date" value="{{ date('Y-m-d') }}" class="form-control" name="from" placeholder="Start date" required>
        </div>
        <div class="col-md-2 mb-2">
            <label class="control-label" for="end-date">To</label>
            <input type="date" value="{{ date('Y-m-d') }}" class="form-control" name="to" placeholder="End date" required>
        </div>


            <div class="col-md-2 mb-2">
                <div class="form-group">
                    <label class="control-label" >City</label>
                    <select class="form-control" name="city" id="city"  onchange="getDistributorByCity()">
                        <option value="">select</option>
                        @foreach ($master->cities() as $row)
                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-2 mb-2">
                <div class="form-group">
                    <label class="control-label" >Distributor Name</label>
                    <select onchange="get_tso()" class="form-control" name="distributor_id" id="distribuotr_id">
                        <option value="">select</option>
                        @foreach ($master->get_all_distributor_user_wise() as $row)
                            <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-2 mb-2">
                <div class="form-group">
                    <label class="control-label" >TSO Name</label>
                    <select class="form-control" id="tso_id" name="tso_id">
                        <option value="">select</option>
                    </select>
                </div>
            </div>

            <div class="col-md-2 mb-3">
                <div class="generate text-left">
                    <button type="button" onclick="get_ajax_data()" class="btn btn-primary btn-xs">Generate</button>
                </div>
            </div>
        </div>

   <!-- Inside the <form id="list_data"> -->
                            <div class="col-md-2 mb-2">
                                <label class="control-label">Search</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    name="search" 
                                    placeholder="Invoice, Distributor, TSO, etc."
                                    value="{{ request('search') }}"
                                    style="
                                width: 160%;
                            "
                                >
                            </div>

    </form>
</div>

    <div class="row" id="table-bordered">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Sale Order List</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" class="form-control" id="CheckUnCheck"></th>
                                <th>Sr No</th>
                                <th>Inoivce No.</th>
                                <th>Inoivce Date.</th>
                             
                                <th>Distributor</th>
                                <th>TSO</th>
                                <th>City</th>
                                <th>Shop</th>
                                <th>Execution</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="data">
                        </tbody>
                    </table>
                </div>
                <div class="create">
               
                </div>
            </div>
        </div>
    </div>
    <!-- Basic Floating Label Form section end -->

@endsection
@section('script')
    <script>
        $(document).ready(function() {
            get_ajax_data();
        });




        
        function get_ajax_data() {
    $.ajax({
        url: "{{ route('sale.index') }}",
        type: "GET",
        data: $("#list_data").serialize(), // Includes search + all other filters
        success: function(response) {
            $("#data").html(response);
        }
    });
}

// Debounce search to avoid excessive AJAX calls
let searchTimer;
$('input[name="search"]').on('input', function() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function() {
        get_ajax_data();
    }, 500); // 500ms delay
});

    </script>
@endsection
