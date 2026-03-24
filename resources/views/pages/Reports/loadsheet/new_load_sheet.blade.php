<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>
@extends('layouts.master')
@section('title', "SND || New Load Sheet")
@section('content')

    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">New Load Sheet</h4>
                    </div>
                    <div class="card-body">
                        <form method="get" action="{{ route('new_load_sheet') }}"  id="list_data" class="form">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>From* </label>
                                        <input type="date" class="form-control" value="{{date('Y-m-d')}}" name="from" id="from">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>To* </label>
                                        <input type="date" class="form-control" value="{{date('Y-m-d')}}"  name="to" id="to">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Distributor Name* </label>
                                        <select required onchange="get_tso()" class="form-control" name="distributor_id" id="distribuotr_id">
                                            <option value="">select</option>
                                            @foreach ( $master->get_all_distributor_user_wise() as $row )
                                                <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>TSO Name</label>
                                        <select class="form-control" id="tso_id" name="tso_id">
                                            <option value="">select</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Execution </label>
                                        <select class="form-control" id="execution" name="execution">
                                            <option value="">select</option>
                                            <option value="1">Yes</option>
                                            <option value="0">NO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button onclick="get_ajax_data()" type="button" class="btn btn-primary mr-1" style="margin-top: 23px;">Generate</button>
                                </div>
                                <div class="col-1">
                                    <button type="button" onclick="printTable('data')" class="btn btn-primary mr-1 text-right right" style="margin-top: 23px;">Print</button>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive" >
                            <div id="data">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('script')
<script>
    function printTable(tableId) {
        var printContents = document.getElementById(tableId).outerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();

        document.body.innerHTML = originalContents;
        location.reload();
    }
</script>
@endsection
