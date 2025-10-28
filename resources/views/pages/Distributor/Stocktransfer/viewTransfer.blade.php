
@extends('layouts.master')
@section('title', 'SND || View Transfer')
<style>

</style>
@section('content')
    <div class="row mb">
        <div class="col-md-12">
            <div class="right" style="float: right">
                <button  id="print" type="button"  class="btn btn-success btn-sm  right">Print</button>
            </div>
        </div>
    </div>


    <div id="content" class="container print">

        <div class="card ptb">

            <h1 class="for-print">Stock Transfer</h1>
            <hr>

            <div class="row">

                <div class="col-md-6 well">

                    <table class="table table table-bordered">
                        <tr>
                            <th>Voucher Number:</th>
                            <td>{{  $stock[0]->voucher_no }}</td>
                        </tr>
                        <tr>
                            <th>Voucher Date:</th>
                            <td>{{ date("d-m-Y", strtotime( $stock[0]->voucher_date))  }}</td>
                        </tr>

                    </table>
                </div>
                <div class="col-md-6 well">
                    <table class="table table-bordered">
                        <tr>
                            <th>Distributor From:</th>
                            <td>{{  $stock[0]->distributor->distributor_name }}</td>
                        </tr>

                        <tr>
                            <th>Distributor To:</th>
                            <td>{{  $stock[0]->distributorsole->distributor_name ?? '' }}</td>
                        </tr>

                    </table>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <h4>Transfer Details</h4>
                    <table class="table table table-bordered">
                        <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Product</th>
                            <th>QTY</th>

                        </tr>
                        </thead>
                        <tbody>

                        @foreach($stock as $key => $row)

                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $row->product->product_name ?? '' }}</td>
                                <td>{{ $row->qty }}</td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
            <br>

        </div>
    </div>
@endsection

