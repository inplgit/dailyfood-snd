@extends('layouts.master')
@section('title', "SND || Update Designation")
@section('content')


<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Update Designation</h4>
                </div>
                <div class="card-body">
                    <form id="subm" method="POST" action="{{ route('designation.update', $designation->id) }}" class="form">
                        @csrf
                        @method('patch')
                        <div class="row">                           
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Name <strong>*</strong></label>
                                    <input name="name" value="{{ $designation->name }}" type="text" class="form-control" placeholder="Designation Name"/>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 mb-1">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="select2 form-control form-control-lg">
                                        <option {{ $designation->status ==  1 ? 'selected' : '' }} value="1">Active</option>
                                        <option {{ $designation->status ==  0 ? 'selected' : '' }} value="0">Deactive</option>
                                    </select>
                                </div>
                            </div>  

                            <div class="col-md-12 seprator">
                                <hr>
                            </div>                           

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary mr-1">Update Item</button>
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

