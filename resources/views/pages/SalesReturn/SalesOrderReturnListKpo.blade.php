@extends('layouts.master')
@section('title', 'Sales Return')
@section('content')

<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>




<form id="list_data" method="get" action="{{isset($excution) ? route('sales_return.sales_return_list_shop_wise', ['excution' => 'execution']) : route('sales_return.index') }}" class="p-3 bg-light border rounded">
    <div class="row g-3 align-items-end">
        <!-- From Date -->
        <div class="col-md-2">
            <label class="form-label fw-bold" for="start-date">From</label>
            <input type="date" value="{{ date('Y-m-d') }}" class="form-control" name="from" placeholder="Start date" required>
        </div>

        <!-- To Date -->
        <div class="col-md-2">
            <label class="form-label fw-bold" for="end-date">To</label>
            <input type="date" value="{{ date('Y-m-d') }}" class="form-control" name="to" placeholder="End date" required>
        </div>

        <!-- City -->
        <div class="col-md-2">
            <label class="form-label fw-bold">City</label>
            <select class="form-control" name="city" id="city"  onchange="getDistributorByCity()">
                <option value="" selected>Select</option>
                @foreach ($master->cities() as $row)
                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Distributor Name -->
        <div class="col-md-2">
            <label class="form-label fw-bold">Distributor Name</label>
            <select class="form-control" name="distributor_id" id="distribuotr_id" onchange="get_tso()">
                <option value="" selected>Select</option>
                @foreach ($master->get_all_distributor_user_wise() as $row)
                    <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- TSO Name -->
        <div class="col-md-2">
            <label class="form-label fw-bold">TSO Name</label>
            <select class="form-control" id="tso_id" name="tso_id">
                <option value="" selected>Select</option>
            </select>
        </div>

        <!-- Generate Button -->
        <div class="col-md-2 text-end">
            <button type="button" onclick="get_ajax_data()" class="btn btn-primary w-100">Generate</button>
        </div>
    </div>
</form>






<form id="list_data" method="get" action="{{route('sales_return.index_return') }}">
        


<div class="row" id="table-bordered">
       
             
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Sales Return List</h4>
                       
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                       
                                        <th>Sr No</th>
                                        <th>Sales Return No</th>
                                      
                                        <th>Distributor</th>
                                        <th>TSO</th>
                                        <th>Shop</th>
                                        <th>Execution</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                                <tbody id="data">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
           
        </div>
    </form>
    <!-- Basic Floating Label Form section end -->


@endsection
@section('script')
   <script>
    function get_ajax_data() {
        $.ajax({
            url: "{{ route('sales_return.index_return') }}",
            type: "GET",
            data: $("#list_data").serialize(),
            success: function (response) {
                $("#data").html(response);
            },
            error: function (xhr) {
                console.error("Error:", xhr.responseText);
            }
        });
    }

    $(document).ready(function () {
        // Call AJAX on page load
        get_ajax_data();

        // ... other click/change handlers if needed ...
    });
</script>

@endsection
