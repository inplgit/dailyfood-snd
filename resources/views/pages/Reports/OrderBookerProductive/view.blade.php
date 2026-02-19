@php

    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
@endphp
@extends('layouts.master')
@section('title', 'TSO Activity')
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Order Booker Productive Status Report</h4>
                        <button type="button" onclick="downloadCSV()"class="btn btn-success">Export Excel</button>
                    </div>
                    <div class="card-body">
                        <form method="get" action="{{ route('order_booker_productive_report') }}" id="list_data" class="form">
                            @csrf
                            <div class="row align-items-end">
                                <div class="col-md-10">
                                    <div class="row ">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">from</label>
                                                <input type="date"  name="from" id="date" class="form-control" value="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>


                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">To</label>
                                                <input type="date"  name="to" id="date" class="form-control" value="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Distributor Name </label>
                                                <select required onchange="get_tso()" class="form-control" name="distributor_id"
                                                    id="distribuotr_id" required>
                                                    <option value="">select</option>
                                                    @foreach ($master->get_all_distributor_user_wise() as $row)
                                                        <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">TSO Name</label>
                                                <select class="form-control" id="tso_id" name="tso_id" required>
                                                    <option value="">select</option>
                                                    {{-- @foreach ($master->get_all_tso() as $row)
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach --}}
                                                </select>
                                            </div>
                                        </div>
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
    $( document ).ready(function() {$('select').select2();});
</script>
@endsection