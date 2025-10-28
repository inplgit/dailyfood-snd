@extends('layouts.master')
@section('title', "SND || Update City")
@section('content')



<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Update City</h4>
                </div>
                <div class="card-body">
                    <form id="subm" method="POST" action="{{ route('city.update', $city->id) }}" class="form">
                        @csrf
                        @method('patch')
                        <div class="row">                           
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Name <strong>*</strong></label>
                                    <input name="name" value="{{ $city->name }}" type="text" class="form-control" placeholder="city Name"/>
                                </div>
                            </div>
                            <div class="col-md-3 col-12 mb-1">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="select2 form-control form-control-lg">
                                        <option {{ $city->status ==  1 ? 'selected' : '' }} value="1">Active</option>
                                        <option {{ $city->status ==  0 ? 'selected' : '' }} value="0">Deactive</option>
                                    </select>
                                </div>
                            </div>  
                            <div class="col-md-12 col-12 mb-1">
                            <label for="out_of_stock_quantity">Continue Selling Even If Out of Stock</label>

                                <div class="form-group">
                                <input type="checkbox" id="out_of_stock_quantity" name="out_of_stock_quantity" value="1"   {{ $city->out_of_stock_quantity ==  1 ? 'checked' : '' }}>
                                    <label for="out_of_stock_quantity">Enable</label>
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

