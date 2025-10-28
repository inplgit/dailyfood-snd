@extends('layouts.master')
@section('title', 'Distributor List')
@section('content')


    <form id="list_data" method="get" action="{{ Request::url('') }}"></form>
    <div class="row" id="table-bordered">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Distributor List</h4>
                    @can('Distributor_Excel_Export')
                        <button type="button" id="exportBtn" onclick="exportBtn('DistributorList')"
                            class="btn btn-success">Export Excel</button>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table id="dataTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 100px">Sr No</th>
                                <th style="width: 100px">Dis. Code</th>
                                <th style="">Dis. Name</th>
                                <th style="">Contact Person</th>
                                <th style="width: 100px">Phone#</th>
                                <th>City</th>
                                <th>Latitude</th>
                                <th>Longitude</th>


                                {{-- <th>Email</th> --}}
                                <th>Status</th>
                                <th style="width: 200px" class="export-hidden">Actions</th>
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
