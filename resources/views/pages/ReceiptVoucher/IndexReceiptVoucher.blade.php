@extends('layouts.master')
@section('title', 'Receipt Voucher')
@section('content')


<div class="card">
    <form id="list_data" method="get" action="{{ route('receipt-voucher.index') }}">

        <div class="row align-items-center">
            <div class="col-md-3 mb-3">
                <label class="control-label" for="start-date" class="control-label">From</label>
                <input type="date" value="{{ date('Y-m-01') }}" class="form-control" name="from" placeholder="Start date"required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="control-label" for="end-date" class="control-label">To</label>
                <input type="date" value="{{ date('Y-m-t') }}" class="form-control" name="to" placeholder="End date"required>
            </div>

            <div class="col-md-3 mb-3">
                <button type="button" onclick="get_ajax_data()" class="btn btn-primary btn-xs">Generate</button>
            </div>
        </div>
    </form>

</div>
    <div class="row" id="table-bordered">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Receipt Voucher List</h4>
                    @can('New_Receipt_Voucher')
                        <a href="{{ route('receipt-voucher.create') }}" class="btn btn-success mr-1">Create</a>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Date</th>
                                <th>Distributor</th>
                                <th>TSO</th>
                                <th>Shop</th>
                                <th>Delivery Man</th>
                                <th>Route</th>
                                <th>Execution</th>
                                <th>Amount</th>
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
    <!-- Basic Floating Label Form section end -->



@endsection
@section('script')
    <script>
        $(document).ready(function() {
            get_ajax_data();
        });
    </script>
@endsection
