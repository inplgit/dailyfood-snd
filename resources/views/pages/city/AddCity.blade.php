@extends('layouts.master')
@section('title', "Add New City")
@section('content')


<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">ADD NEW City</h4>                    
                </div>
                <div class="card-body">
                    <form id="subm" method="POST" action="{{ route('city.store') }}" class="form">
                        @csrf
                        <div class="row">                           
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Name<strong>*</strong></label>
                                    <input name="name" value="{{ old('name') }}" type="text" class="form-control" placeholder="city Name"/>
                                </div>
                            </div>

                            <input name="state_id" value="2729" type="hidden" class="form-control" />

                            <div class="col-md-3 col-12 mb-1">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="select2 form-control form-control-lg">
                                        <option value="1">Active</option>
                                        <option value="0">Deactive</option>
                                    </select>
                                </div>
                            </div>  

                            <div class="col-md-12 col-12 mb-1">
                            <label for="out_of_stock_quantity">Continue Selling Even If Out of Stock</label>

                                <div class="form-group">
                                <input type="checkbox" id="out_of_stock_quantity" name="out_of_stock_quantity" value="1">
                                    <label for="out_of_stock_quantity">Enable</label>
                                </div>
                            </div>  

                            <div class="col-md-12 seprator">
                                <hr>
                            </div>                           

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary mr-1">Create city</button>
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

