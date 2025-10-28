@extends('layouts.master')
@section('title', 'Order Booker List')
@section('content')


    <form id="list_data" method="get" action="{{ route('tso.index') }}">


    </form>
    <div class="row" id="table-bordered">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Order Booker List</h4>
                    @can('TSO_Excel_Export')
                        <button type="button" id="exportBtn" onclick="exportBtn('TSOList')" class="btn btn-success">Export
                            Excel</button>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table id="dataTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Order Booker Code</th>
                                <th>Order Booker/Employee Name</th>
                                <th>Designation</th>
                                <th>City</th>

                                <th>CNIC</th>
                                <th>Phone NO</th>
                                <th>Distributor</th>
                                <th>User ID</th>
                                <th>Status</th>
                                <th>Date Of Join </th>
                                <th>Date Of Leaving</th>
                                <th class="export-hidden">Actions</th>
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
