<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>
@extends('layouts.master')
@section('title', "SND || Caregory")
@section('content')


    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Order Booker Target Sheet</h4>
                    </div>
                    <div class="card-body">
                        <form method="get" action="{{ route('tso_target') }}"  id="list_data" class="form">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>From Month* </label>
                                        <input type="month" class="form-control" value="{{date('Y-m')}}" name="from" id="">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> To Month* </label>
                                        <input type="month" class="form-control" value="{{date('Y-m')}}" name="to" id="">
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

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>TSO Name</label>
                                        <select class="form-control" id="tso_id" name="tso_id">
                                            <option value="">select</option>
                                            {{-- @foreach ($master->get_all_tso() as $row )
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach --}}

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Target Type</label>
                                        <select class="form-control" id="target_type" name="target_type">
                                            <option value="1">Quantity</option>
                                            <option value="2">Amount</option>
                                            <option value="3">Shop</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Summary</label>
                                        <select class="form-control" id="yes" name="summary">
                                            <option value="0">TARGET</option>
                                            <option value="1">TARGET VS ACHEIVED </option>
                                        </select>
                                        {{-- <input type="radio" id="yes" name="summary" value="1">
                                        <label for="yes">yes</label>
                                          <input type="radio" id="no" checked name="summary" value="0">
                                        <label for="no">No</label> --}}
                                    </div>
                                </div>
                                <div class="col-md-1">

                                    <button onclick="get_ajax_data()" type="button" class="btn btn-primary mr-1">Generate</button>
                                </div>
                                <div class="col-1">
                                <button type="button" onclick="printTable('print_data')" class="btn btn-primary mr-1 text-right right">Print</button>



                            </div>


                            </div>
                        </form>
                        <form method="post" action="{{ route('execution.multi_so_view') }}">
                            @csrf
                        <div class="table-responsive" id="data">






                        </div>
                           <br>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary mr-1 text-right right">View</button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Floating Label Form section end -->

@endsection

<script>
    function printTable(tableId) {
        var printContents = document.getElementById(tableId).outerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();

        document.body.innerHTML = originalContents;
    }
</script>
