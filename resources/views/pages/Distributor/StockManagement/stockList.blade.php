@extends('layouts.master')
@section('title', 'Stock List')
@section('content')





    <div class="card">
            <form id="list_data" method="get" action="{{ route('stockManagement.index') }}">

                <div class="row">
                    <div class="col-md-3">
                        <select name="distributor_id" class="form-control" id="distributor_id">
                            <option value="">select one</option>
                            @foreach ($distributors as $distributor)
                                <option value="{{ $distributor->id }}">{{ $distributor->distributor_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="Stock List_card">
                            <button  type="button" name="submit" value="submit" class="btn btn-primary btn-xs waves-effect waves-float waves-light" onclick="get_ajax_data()">
                                Genrate
                            </button>
                        </div>

                    </div>
                </div>
            </form>
    </div>


    <div class="row" id="table-bordered">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Stock List</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Product Name</th>
                                <th>Product Flavour</th>
                                <th>Qty</th>
                                <th>Sales Price</th>
                                <th>Amount</th>
                                <th>Discount %</th>
                                <th>Net Amount </th>
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
            // get_ajax_data();
        });
    </script>
@endsection
