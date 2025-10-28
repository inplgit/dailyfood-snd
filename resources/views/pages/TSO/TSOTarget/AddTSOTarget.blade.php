target<?php
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
                    <h4 class="card-title">Order Booker Target</h4>
                </div>

                    <div class="card-body">

                        <form id="list_data"  method="get" action="{{ Request::url('') }}">
                            <div class="row">

                            <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">City </label>
                                                <select class="form-control" name="city" id="city"  onchange="getDistributorByCity()">
                                                    <option value="">select</option>
                                                    @foreach ($master->cities() as $row)
                                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Distributor Name </label>
                                                <select onchange="get_tso()" class="form-control" name="distributor_id" id="distribuotr_id">
                                                    <option value="">select</option>
                                                    @foreach ($master->get_all_distributor_user_wise() as $row)
                                                        <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>


                                        </div>
                            <div class="col-md-3">

                                    <label>Order Booker Name</label>
                                <!-- <select class="form-control" id="tso_id"   name="tso_id">
                                    <option value="">select</option>
                                    @foreach ($master->get_tso_distribuor_wise()->where('active' , 1) as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select> -->

                                <select class="form-control" id="tso_id" name="tso_id">
                                            <option value="">select</option>
                                            {{-- @foreach ($master->get_all_tso() as $row )
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach --}}

                                        </select>
                                </div>

                                <div  class="col-md-3">
                                    <label>Month</label>
                                    <input   class="form-control" type="month" name="month" id="">
                                </div>
                                {{-- <div class="col-md-3">
                                    <div class="form-group">
                                        <label> Shop Type* </label>
                                        <select name="shop_type_id"   class="select2 form-control form-control-lg">
                                            <option value="">select</option>
                                            @foreach ( $master->get_all_shop_type() as $row )
                                            <option value="{{ $row->id }}">{{ $row->shop_type_name }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div> --}}
                                <div class="col-md-3">
                                    <button style="
    margin-top: 23px;
" class="btn btn-primary" onclick="get_ajax_data()" type="button">get</button>
                                </div>
                            </div>
                        </form>


                        </br>
                        <form id="subm"  method="get" action="{{ route('tso-target.store') }}">
                            @csrf
                            <div id="data">

                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary mr-1">Create Target</button>
                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            </div>
                        </form>
                    </div>

            </div>
        </div>
    </div>
</section>





@endsection
@section('script')
<script>
    document.getElementById('subm').addEventListener('submit', function (e) {
        const city = document.getElementById('city').value;
        if (!city) {
            e.preventDefault();
            alert("City is required to create a target.");
        }
    });
</script>
@endsection
