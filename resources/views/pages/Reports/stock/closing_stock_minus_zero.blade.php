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
                        <h4 class="card-title">Closing Stock Adjustment</h4>
                    </div>
                    <div class="card-body">
<form method="POST" action="{{ route('closing_stock_minus_zero_sub') }}" onsubmit="return confirm('Are you sure you want to closing the stock to zero?')">



                            <!-- <form method="POST" action="{{ route('closing_stock_minus_zero_sub') }}"> -->
                                @csrf
                             
                                <div class="col-md-3">
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
                                        <label>Product </label>
                                        <select  class="form-control" name="product_id"
                                            id="product_id" >
                                            <option value="">All</option>
                                            @foreach ($master->get_all_product() as $row)
                                                <option value="{{ $row->id }}">{{ $row->product_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                            </div>

                               
                                        <div class="col-md-1" style="margin-top: 23px;">
                                    <button type="submit" class="btn btn-primary mr-1">Submit</button>
                                </div>
                            </form>

                            </div>
                        </form>
                      
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Floating Label Form section end -->

@endsection
<script src="{{ URL::asset('assets/js/xlsx.js') }}"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
 <script>
    $(document).ready(function(){
        $('#distribuotr_id').select2();
        $('#product_id').select2();
    })
 </script> 

