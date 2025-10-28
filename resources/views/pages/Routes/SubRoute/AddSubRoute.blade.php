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
                                    <h4 class="card-title">Add Sub Route</h4>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="{{ route('subroutes.store') }}"  id="subm" class="form">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="main_head">
                                                    <h2>Sub Route List</h2>
                                                </div>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Route Head</label>
                                                        <select  class="form-control" name="route_id" id="route_id">
                                                            <option value="">select</option>
                                                            @foreach ( $route as $row )
                                                            <option value="{{ $row->id }}">{{ $row->route_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        </div>
                                                    </div>
        
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">Sub Route Name</label>
                                                            <input type="text" name="name" class="form-control" placeholder="Sub Route Name"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-md-12 mt-2 text-right">
                                                <button type="submit" class="btn btn-primary mr-1">Create Sub Route</button>
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

$( document ).ready(function() {
    $('#route_id').select2();
});
</script>
@endsection

