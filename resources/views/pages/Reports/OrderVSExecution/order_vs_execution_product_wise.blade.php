@php

    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
    use App\Models\Product;
@endphp
@extends('layouts.master')
@section('title', 'Order vs Execution Product Wise')
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Order vs Execution (Product Wise) </h4>
                        <button type="button" id="exportBtn" onclick="exportBtnWithFilters('Order vs Execution Product Wise')"
                            class="btn btn-success">Export Excel</button>
                    </div>
                    <div class="card-body">
                        <form method="get" action="{{ route('order_vs_execution_product_wise') }}" id="list_data" class="form">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>From</label>
                                        <input type="date" name="from" id="date" class="form-control"
                                            value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>To</label>
                                        <input type="date" name="to" id="date" class="form-control"
                                            value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Distributor Name </label>
                                        <select required onchange="get_tso()" class="form-control" name="distributor_id"
                                            id="distribuotr_id" required>
                                            <option value="">All</option>
                                            @foreach ($master->get_all_distributor_user_wise() as $row)
                                                <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>TSO Name</label>
                                        <select onchange="get_route_by_tso()" class="form-control" id="tso_id"
                                            name="tso_id" required>
                                            <option value="">All</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Route</label>
                                        <select onchange="get_shop_by_route()" class="form-control" id="route_id"
                                            name="route_id" required>
                                            <option value="">All</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="cust2">
                                        <label class="control-label">Shop</label>
                                        <select class="form-control select2" id="shop_id" name="shop_id" required="">
                                            <option value="">Select a Shop </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>City </label>
                                        <select required class="form-control" name="city" id="" required>
                                            <option value="">select</option>
                                            @foreach ($master->cities() as $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button onclick="get_ajax_data()" type="button" class="btn btn-primary mr-1">Generate</button>
                                    <button type="button" onclick="printTableNew('.printBody')" class="btn btn-primary text-right"> Print </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">


                    <div id="data">

                    </div>
                </div>
            </div>
        </div>




    </section>
    <!-- Basic Floating Label Form section end -->
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('select').select2();
        });
    </script>
@endsection
