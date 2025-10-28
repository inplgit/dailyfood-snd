<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>
@extends('layouts.master')
@section('title', 'Bill Printing')
@section('content')


<div class="card">
    <form method="get" action="{{ route('execution.bill_printing') }}" id="list_data" class="form">
        @csrf
        <div class="row">
            @if(Session::has('catchError'))
                <span class="invalid-feedback" role="alert" style="display: block;">
                    <strong>{{ Session::get('catchError') }}</strong>
                </span>
            @endif
            <div class="col-md-2">
                <div class="form-group">
                    <label class="control-label">From* </label>
                    <input type="date" class="form-control" value="{{ date('Y-m-d') }}"
                        name="from" id="">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label class="control-label">To* </label>
                    <input type="date" class="form-control" value="{{ date('Y-m-d') }}"
                        name="to" id="">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label class="control-label">Distributor Name* </label>
                    <select required onchange="get_tso()" class="form-control" name="distributor_id"
                        id="distribuotr_id">
                        <option value="">select</option>
                        @foreach ($master->get_all_distributor_user_wise() as $row)
                            <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label class="control-label">TSO Name</label>
                    <select class="form-control" id="tso_id" name="tso_id">
                        <option value="">select</option>
                        {{-- @foreach ($master->get_all_tso() as $row)
                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                        @endforeach --}}

                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="gers">
                     <button onclick="get_ajax_data()" type="button" class="btn btn-primary mr-1">Generate</button>
                </div>
            </div>

        </div>
    </form>

</div>


    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Bill Printing</h4>
                    </div>



                    <div class="card-body">

                        <form method="post" action="{{ route('execution.multi_so_view') }}">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" class="checkbox text-center" class="check"></th>
                                            <th>Sr No</th>
                                            <th>Inoivce No.</th>
                                            <th>Inoivce Date.</th>
                                            <th>Distributor</th>
                                            <th>TSO</th>
                                            <th>Shop</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="data">
                                    </tbody>
                                </table>
                            </div>
                            <br>
                            <div class="col-12">
                                @can('Bill_Printing_Execute_View')
                                    <button type="submit" class="btn btn-primary mr-1 text-right right">View</button>
                                @endcan
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Floating Label Form section end -->

@endsection
