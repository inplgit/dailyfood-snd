@extends('layouts.master')
@section('title', 'Route')
@section('content')


    <form id="list_data" method="get" action="{{ Request::url('') }}"></form>
    <div class="row" id="table-bordered">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Route List</h4>
                    @can('Route_Excel_Export')
                        <button type="button" id="exportBtn" onclick="exportBtn('RouteList')" class="btn btn-success">Export
                            Excel</button>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table id="dataTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Route Name</th>
                                <th>City</th>
                                <th>Distributor Name</th>
                                <th>TSO Name</th>
                                <th>Days</th>
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
