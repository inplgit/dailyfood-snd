@extends('layouts.master')
@section('title', 'Add Zone')
@section('content')
<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">ADD NEW ZONE</h4>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('zone.store') }}" id="subm" class="form">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Zone Name</label>
                                    <input type="text" name="zone_name" class="form-control" placeholder="zone Name" />
                                </div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <button type="submit" class="btn btn-primary mr-1">Create</button>
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
