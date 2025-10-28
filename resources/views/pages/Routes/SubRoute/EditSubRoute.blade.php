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
                                    <h4 class="card-title">Edit Sub Route</h4>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="{{ route('subroutes.update',$subroutes) }}"  id="subm" class="form">

                                        @csrf
                                        @method('patch')
                                        <div class="row">


                                            <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Route Head</label>
                                               <select  class="form-control" name="route_id" id="route_id">
                                                <option value="">select</option>
                                                @foreach ( $route as $row )
                                                 <option @if($subroutes->route_id == $row->id) selected @endif  value="{{ $row->id }}">{{ $row->route_name }}</option>
                                                @endforeach
                                               </select>
                                            </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Sub Route Name</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $subroutes->name }}" placeholder="Sub Route Name"/>
                                                </div>
                                            </div>


                                            <div class="col-12">
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

