<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>
@extends('layouts.master')
@section('title', 'SND || Caregory')
@section('content')
<div class="card">

    <form id="list_data" method="get" action="{{ url('report/top_product_report/' . $id) }}">
        <div class="form-row">
            <div class="col-md-2 mb-2">
                <label  class="control-label" for="start-date">From</label>
                <input type="date" value="{{ date('Y-m-01') }}" class="form-control" name="from" placeholder="Start date"
                    required>
            </div>
            <div class="col-md-2 mb-2">
                <label  class="control-label" for="end-date">To</label>
                <input type="date" value="{{ date('Y-m-t') }}" class="form-control" name="to" placeholder="End date"
                    required>
            </div>

            <div class="col-md-2 mb-3">
                <div class="generate text-left">
                    <button type="button" onclick="get_ajax_data()" class="btn btn-primary btn-xs">Generate</button>
                </div>
            </div>
            <div class="col-md-5 mb-3">
            </div>
            <div class="col-md-1 mb-3">
                <button type="button" onclick="printTable('table-bordered')" class="btn btn-primary mr-1 text-right right">Print</button>



            </div>
        </div>

    </form>
</div>
    <div class="row" id="table-bordered">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{$product->product_name}} (Report)</h4>
                </div>
                <div class="table-responsive" style="height:auto !important;">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                {{-- <th><input type="checkbox" class="form-control" id="CheckUnCheck"></th> --}}
                                <th>Sr No</th>
                                <th>Inoivce No.</th>
                                <th>Inoivce Date.</th>
                                <th>Distributor</th>
                                <th>TSO</th>
                                {{-- <th>City</th> --}}
                                <th>Shop</th>
                                <th>Quantity</th>
                                {{-- <th>Execution</th> --}}
                                <th >Total</th>
                                {{-- <th>Actions</th> --}}
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

    function printTable(tableId) {
        var printContents = document.getElementById(tableId).outerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();

        document.body.innerHTML = originalContents;
    }

</script>
@endsection
