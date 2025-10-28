@extends('layouts.master')
@section('title', "Add New Designation")
@section('content')


<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Config Setting</h4>
                </div>
                <div class="card-body">
                    <form id="subm" method="POST" action="{{ route('config.store') }}" class="form">
                        @csrf
                        <div class="row">
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Order Booker Max Limit<strong>*</strong></label>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 mb-1">
                                <div class="form-group">
                                    <input name="tso_max_limit" value="{{$config['tso_max_limit'] ?? ''}}" type="text" class="form-control" placeholder="Order Booker Max Limit"/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 seprator">
                                <hr>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary mr-1">Save</button>
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

