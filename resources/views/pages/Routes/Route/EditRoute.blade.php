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
                                    <h4 class="card-title">Edit  Route</h4>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="{{ route('route.update',$route->id) }}"  id="subm" class="form">
                                        @method('PUT')
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Route Name</label>
                                                    <input type="text" value="{{ $route->route_name }}" name="route_name" class="form-control" placeholder="Route Name"/>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Distributor Name</label>
                                               <select onchange="get_tso()" class="form-control" name="distributor_id" id="distribuotr_id">
                                                <option value="">select</option>
                                                @foreach ( $master->get_all_distributor_user_wise() as $row )
                                                 <option @if($route->distributor_id==$row->id) selected @endif value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                                                @endforeach
                                               </select>
                                            </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>TSO Name</label>
                                                    <select class="form-control" name="tso_id" id="tso_id">
                                                        <option value="">select</option>
                                                        @foreach ($master->get_all_tso_by_distributor_id($route->distributor_id) as $row )
                                                        <option @if($route->tso_id==$row->id) selected @endif value="{{ $row->id }}">{{ $row->name }}</option>
                                                        @endforeach

                                                       </select>
                                                </div>
                                                </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Days</label>

                                                    <select class="form-control" multiple id="day" name="day[]">
                                                        <option value="">select</option>
                                                        @foreach ( $master->Days() as $row )
                                                        <option @if(in_array($row, $route_day)) selected @endif   value="{{ $row }}">{{ $row }}</option>
                                                        @endforeach
                                                       </select>
                                                </div>
                                                </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary mr-1">Update Route</button>
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
