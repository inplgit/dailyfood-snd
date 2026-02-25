@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
@endphp

@extends('layouts.master')

@section('title', 'Shop Summary Report')

@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Shop Summary Report</h4>
                        <button type="button" class="btn btn-success" onclick="exportBtnWithFilters('Shop Summary Report')">Export Excel</button>
                    </div>
                    <div class="card-body mt-2">
                        <form method="get" action="{{ route('shop_summary_report') }}" id="list_data" class="form">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Distributor Name</label>
                                        <select class="form-control select2" name="distributor_id" id="distribuotr_id" onchange="get_tso()">
                                            <option value="">All</option>
                                            @foreach ($master->get_all_distributor_user_wise() as $row)
                                                <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>TSO Name</label>
                                        <select class="form-control select2" id="tso_id" name="tso_id" onchange="get_route_by_tso(); get_shop_by_tso();">
                                            <option value="">All</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Route</label>
                                        <select onchange="get_shop_by_route()" class="form-control select2" id="route_id" name="route_id">
                                            <option value="">All</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Shop</label>
                                        <select class="form-control select2" id="shop_id" name="shop_id">
                                            <option value="">All</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Type</label>
                                        <select class="form-control select2" name="type" id="type">
                                            <option value="category" selected>Shop Category</option>
                                            <option value="class">Shop Class</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <div class="form-group">
                                        <button onclick="get_ajax_data()" type="button" class="btn btn-primary">Generate</button>
                                        <button type="button" onclick="printTableNew('.printBody')" class="btn btn-primary ml-1"> Print </button>
                                    </div>
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
                    <div id="data"></div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endsection
