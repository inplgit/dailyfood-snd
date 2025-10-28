@extends('layouts.master')
@section('title', "SND || Caregory")
@section('content')


    <form id="list_data"  method="get" action="{{ Request::url('') }}"></form>
    <div class="row" id="table-bordered">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Sales Return  List</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Sales Return No</th>
                            <th>SO NO</th>
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
                <!-- Basic Floating Label Form section end -->



@endsection
@section('script')
    <script>
        $(document).ready(function() {
            get_ajax_data();
        });

    </script>
@endsection
