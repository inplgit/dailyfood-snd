@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
@endphp

@extends('layouts.master')

@section('title', 'Shops Amount Wise Report')

@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">Shops Amount Wise Report</h4>
                        <button type="button" class="btn btn-success" onclick="exportBtn('Shops Amount Wise Report')">Export Excel</button>
                    </div>
                    <div class="card-body mt-2">
                        <form method="get" action="{{ route('shops_amount_wise_report') }}" id="list_data" class="form">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>From</label>
                                        <input type="date" name="from" id="from" class="form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>To</label>
                                        <input type="date" name="to" id="to" class="form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
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
                                        <select class="form-control select2" id="tso_id" name="tso_id" onchange="get_shop_by_tso()">
                                            <option value="">All</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Shop Name</label>
                                        <select class="form-control select2" id="shop_id" name="shop_id">
                                            <option value="">All</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Amount Range</label>
                                        <select class="form-control select2" name="amount_range" id="amount_range">
                                            <option value="">All</option>
                                            <option value="0 - 600">0 - 600</option>
                                            <option value="600 - 1200">600 - 1200</option>
                                            <option value="1200 - 1800">1200 - 1800</option>
                                            <option value="1800 - 3000">1800 - 3000</option>
                                            <option value="3000 - 4000">3000 - 4000</option>
                                            <option value="4000 - max">4000 - max</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <div class="form-group w-100">
                                        <div class="d-flex justify-content-between">
                                            <button onclick="get_ajax_data()" type="button" class="btn btn-primary" style="width: 49%; white-space: nowrap;">Generate</button>
                                            <button type="button" onclick="printTableNew('.printBody')" class="btn btn-primary text-right"> Print </button>
                                        </div>
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
