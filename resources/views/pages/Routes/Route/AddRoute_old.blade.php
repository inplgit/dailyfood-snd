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
                                    <h4 class="card-title">Add  Route</h4>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="{{ route('route.store') }}"  id="subm" class="form">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="main_head">
                                                    <h2>Route Details</h2>
                                                </div>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Route Name</label>
                                                            <input type="text" name="route_name" class="form-control" placeholder="Route Name"/>
                                                        </div>
                                                    </div>
        
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Distributor Name</label>
                                                        <select onchange="get_tso()" class="form-control" name="distributor_id" id="distribuotr_id">
                                                            <option value="">select</option>
                                                            @foreach ( $master->get_all_distributor_user_wise() as $row )
                                                            <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        </div>
                                                    </div>
        
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">TSO Name</label>
                                                            <select class="form-control" id="tso_id" name="tso_id">
                                                                <option value="">select</option>
                                                                {{-- @foreach ($master->get_all_tso() as $row )
                                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                                @endforeach --}}
        
                                                               </select>
                                                        </div>
                                                    </div>
        
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Days</label>
                                                            <select class="form-control" multiple name="day[]" id="day">
                                                                <option value="">select</option>
                                                                @foreach ( $master->Days() as $row )
                                                                <option value="{{ $row }}">{{ $row }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                               
                                            <div class="col-md-12 mt-2 text-right">
                                                <button type="submit" class="btn btn-primary mr-1">Create Route</button>
                                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Basic Floating Label Form section end -->

@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#day').select2();
        });
    </script>
@endsection
