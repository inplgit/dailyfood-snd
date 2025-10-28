@extends('layouts.master')
@section('title', "SND || Caregory")
@section('content')


                <section id="multiple-column-form">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Edit  Shop Type</h4>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="{{ route('shoptype.update',$shoptype->id) }}"  id="subm" class="form">
                                        @method('PUT')
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Shop Type Name</label>
                                                    <input type="text" name="shop_type_name" value="{{ $shoptype->shop_type_name }}" class="form-control" placeholder="Shop Type Name"/>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary mr-1">Update</button>
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

