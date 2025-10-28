{{--
@extends('layouts.master')
@section('title', 'SND || View Receipt Voucher')


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

    <h1 class="for-print">Receipt Voucher</h1>
    <hr>

    <div class="row">

        <div class="col-md-6 well">

            <table class="table table table-bordered">
                <tr>
                    <th style="width:50%;">Receipt Voucher:</th>
                    <td style="width:50%;">{{  $receipt_voucher->distributor->distributor_name ?? '' }}</td>
                </tr>
                <tr>
                    <th style="width:50%;">Delivery Man:</th>
                    <td style="width:50%;">{{ $receipt_voucher->deliveryMan->name ?? '' }}</td>
                </tr>

            </table>
        </div>
        <div class="col-md-6 well">
            <table class="table table-bordered">
                <tr>
                    <th style="width:50%;">TSO:</th>
                    <td style="width:50%;">{{  $receipt_voucher->tso->name ?? '' }}</td>
                </tr>
                <tr>
                    <th style="width:50%;">Route:</th>
                    <td style="width:50%;">{{  $receipt_voucher->route->route_name ?? '' }}</td>
                </tr>
                </table>
        </div>
        <div class="col-md-6 well">
            <table class="table table-bordered">

                <tr>
                    <th style="width:50%;">Issue Date:</th>
                    <td style="width:50%;">{{   date("d-m-Y", strtotime($receipt_voucher->issue_date)) ?? '' }}</td>
                </tr>

                <tr>
                    <th style="width:50%;">Amount:</th>
                    <td style="width:50%;">{{  $receipt_voucher->amount }}</td>
                </tr>
                <tr>
                    <th>Mode Of Payment:</th>
                    <td>{{  $receipt_voucher->mode_of_payment }}</td>
                </tr>
                </table>
        </div>
        <div class="col-md-6 well">
            <table class="table table-bordered">
                <tr>
                    <th style="width:50%;">Execution:</th>
                    <td style="width:50%;">{{  $receipt_voucher->execution }}</td>
                </tr>
                <tr>
                    <th style="width:50%;">Details:</th>
                    <td style="width:50%;">{{  $receipt_voucher->detail }}</td>
                </tr>


            </table>
        </div>
    </div>



<br>


    </div>
</div>
@endsection
 --}}

 <div style="" id="content" class="container print">
    <hr>

    <div class="card ptb">
        <div class="head_main">
            <h1 class="for-print">View Sales Order</h1>
        </div>
        <div class="logo_snd">
            <a class="navbar-brand" href="{{ url('dashboard') }}">
                <span class="brand-logo">
                    <img src="{{ url('/public/assets/images/logo2.png') }}">
                </span>
            </a>
        </div>
        <br>
        <h1 class="for-print">Receipt Voucher</h1>
        <hr>
        <div class="row">
            <div class="col-md-6 well">
                <table class="table table table-bordered">
                    <tr>
                        <th>Receipt Number:</th>
                        <td>{{  $receipt_voucher->id ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Issue Date:</th>
                        <td>{{  $receipt_voucher->issue_date ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Amount:</th>
                        <td>{{  $receipt_voucher->amount ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Mode Of Payment:</th>
                        <td>{{  $receipt_voucher->mode_of_payment }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6 well">
                <table class="table table-bordered">
                    <tr>
                        <th>Distributor:</th>
                        <td>{{  $receipt_voucher->distributor->distributor_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>TSO:</th>
                        <td>{{$receipt_voucher->tso->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Route:</th>
                        <td>{{  $receipt_voucher->route->route_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Sub Route:</th>
                        <td>{{  $receipt_voucher->route->subroute->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Shop:</th>
                        <td>{{  $receipt_voucher->shop->company_name ?? '' }}</td>
                    </tr>

                </table>
            </div>
        </div>
        <hr>

    </div>
</div>
