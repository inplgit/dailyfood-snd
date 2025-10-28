@extends('layouts.master')
@section('title', "SND || Caregory")
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">ADD NEW UOM</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('uom.store') }}"  id="subm" class="form">
                            @csrf
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">UOM Name</label>
                                    <input type="text" name="uom_name" class="form-control" placeholder="UOM Name"/>
                                </div>
                            </div>
                            <div class="col-md-12 mt-4">
                                <button type="submit" class="btn btn-primary mr-1">Create Item</button>
                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Floating Label Form section end -->

@endsection

