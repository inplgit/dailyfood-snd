@extends('layouts.master')
@section('title', "Product")
@section('content')




    <form id="list_data"  method="get" action="{{ Request::url('') }}"></form>
    <div class="row" id="table-bordered">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Product  List</h4>
                </div>
                <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                        <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Category</th>
                            <th>Product Name</th>
                            <th>Brand</th>
                            <th>Product Type</th>
                            <th>UOM</th>
                            <th>Trade Price</th>
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
