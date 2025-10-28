@extends('layouts.master')
@section('title', "SND || Caregory")
@section('content')


    <form id="list_data"  method="get" action="{{ Request::url('') }}"></form>
    <div class="row" id="table-bordered">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Sub Route List</h4>
                    <button type="button" id="exportBtn" onclick="exportBtn('RouteList')" class="btn btn-success">Export Excel</button>
                </div>
                <div class="table-responsive">
                    <table id="dataTable" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Route Head</th>
                            <th>Sub Route</th>

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
