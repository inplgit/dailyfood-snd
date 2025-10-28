<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper;
?>
@extends('layouts.master')
@section('title', 'SND || Caregory')
@section('content')
<section id="multiple-column-form">
                     <section id="multiple-column-form">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">ADD NEW SHOP</h4>
                                    </div>
                                    <div class="card-body">
                                        <form id="subm" method="post" action="{{ route('shop.update',$shop->id) }}" class="form">
                                            @method('PUT')
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Shop Code</label>
                                                        <input name="shop_code" value="{{ $shop->shop_code }}" readonly type="text" val class="form-control" placeholder="Shop Code"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Custom Code (Optional)</label>
                                                        <input value="{{ $shop->custome_code }}" name="custome_code" type="text" class="form-control" placeholder="Custom Code"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Shop Name</label>
                                                        <input value="{{ $shop->company_name }}" name="company_name" type="text" class="form-control" placeholder="Company Name"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Title</label>
                                                        <input value="{{ $shop->title }}" type="text" name="title" class="form-control" placeholder="Title">

                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Contact Person</label>
                                                        <input  value="{{ $shop->contact_person }}" name="contact_person" type="text" class="form-control" placeholder="Contact Person"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Email</label>
                                                        <input value="{{ $shop->email }}" name="email" type="email" class="form-control" placeholder="Email"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Alt. Email</label>
                                                        <input value="{{ $shop->alt_email }}" name="alt_email" type="email" class="form-control" placeholder="Alt. Email"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Phone #</label>
                                                        <input value="{{ $shop->phone }}" name="phone" type="text" class="form-control" placeholder="Phone #"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Mobile No.</label>
                                                        <input value="{{ $shop->mobile_no }}" name="mobile_no" type="text" class="form-control" placeholder="Mobile No."/>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Alt. Phone</label>
                                                        <input value="{{ $shop->alt_phone }}" name="alt_phone" type="text" class="form-control" placeholder="Alt. Phone"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Address</label>
                                                        <input value="{{ $shop->address }}" name="address" type="text" class="form-control" placeholder="Address"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Address Line 2</label>
                                                        <input value="{{ $shop->address_2 }}" name="address_2" type="text" class="form-control" placeholder="Address Line 2"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>City</label>
                                                        <select class="form-control" name="city" id="city">
                                                            <option value="">Select</option>
                                                            @foreach ($master->cities() as $row)
                                                            <option @if($shop->city == $row->id) selected @endif value="{{ $row->id }}">{{ $row->name }}</option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>State</label>
                                                        <input value="{{ $shop->state }}" name="state" type="text" class="form-control" placeholder="State"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>ZIP</label>
                                                        <input value="{{ $shop->zip_code }}" name="zip_code" type="text" class="form-control" placeholder="ZIP"/>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 col-12">
                                                    <div class="form-group">
                                                        <label>Notes</label>
                                                        <textarea  name="note" class="form-control" placeholder="Notes">{{ $shop->note }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 seprator">
                                                    <hr>
                                                </div>

                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>This Shop is Tax Filer / Registered?</label>
                                                        <select name="filer" class="select2 form-control form-control-lg">
                                                            <option @if($shop->filer==1) selected @endif value="1">Yes</option>
                                                            <option @if($shop->filer==0) selected @endif value="0">No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>CNIC</label>
                                                        <input value="{{ $shop->cnic }}" name="cnic" type="text" class="form-control" placeholder="CNIC" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Allowed Credit (No of Days)</label>
                                                        <input value="{{ $shop->allow_credit_days }}" name="allow_credit_days" type="text" class="form-control" placeholder="Allowed Credit (No of Days)" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Allowed Credit (Amount)</label>
                                                        <input value="{{ $shop->allow_credit_amount }}" name="allow_credit_amount" type="text" class="form-control" placeholder="Allowed Credit (Amount)" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>No of delivery days</label>
                                                        <input value="{{ $shop->delvery_days }}" name="delvery_days" type="text" class="form-control" placeholder="Allowed Credit (Amount)" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Invoice Discount.</label>
                                                        <input value="{{ $shop->invoice_discount }}" name="invoice_discount" type="text" class="form-control" placeholder="Allowed Credit (Amount)" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Shop Type</label>
                                                        <select name="shop_type_id" class="select2 form-control form-control-lg">
                                                            <option value="">select</option>
                                                           @foreach ($master->get_all_shop_type() as $row )
                                                             <option @if($shop->shop_type_id==$row->id) selected @endif value="{{ $row->id }}">{{ $row->shop_type_name }}</option>
                                                           @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Shop Zone</label>
                                                        <select name="shop_zone_id" class="select2 form-control form-control-lg">
                                                            <option value="">select</option>
                                                            @foreach ($master->get_all_zone() as $row)
                                                            <option @if($shop->shop_zone_id==$row->id) selected @endif value="{{ $row->id }}">{{ $row->zone_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label class="control-label">Opening Balance Amount.</label>
                                                        <input name="balance_amount" min="0" type="number" step="any" class="form-control" placeholder="Balance Amount" value="{{ $shop->balance_amount }}" />
                                                    </div>
                                                </div>

                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label class="control-label">Opening Balance Type.</label>
                                                        <select name="debit_credit"  class="form-control" id="">
                                                            <option value="1" {{ $shop->debit_credit == 1 ? 'selected' : '' }} >Debit</option>
                                                            <option value="2" {{ $shop->debit_credit == 2 ? 'selected' : '' }}>Credit</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Photo</label>
                                                        <input type="file" name="image" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-12 seprator">
                                                    <hr>
                                                </div>
                                            </div>
                                                <div class="row">
                                                    <div class="col-md-3 col-12">
                                                        <div class="form-group">
                                                            <label class="control-label" for="shop_location">Shop Location.</label>
                                                            <input type="checkbox"  name="shop_location" id="shop_location" onclick="shopLocation()" value="1" {{$shop->latitude != null && $shop->longitude != null && $shop->location_radius != null ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row get_location" style="display: {{$shop->latitude != null && $shop->longitude != null && $shop->location_radius != null ? '' : 'none' }};">
                                                    <div class="col-md-12 map-tb">
                                                        <input type="text" value="{{ $shop->map }}" name="map" class="form-control controls" placeholder="Map name" id="pac-input"/>

                                                        <div id="map"></div>
                                                    </div>



                                                    <div class="col-md-3 col-12">
                                                        <div class="form-group">
                                                            <label>Latitude</label>
                                                            <input value="{{ $shop->latitude }}"  name="latitude" readonly id="lat" type="text" class="form-control" placeholder="Latitude"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 col-12">
                                                        <div class="form-group">
                                                            <label>Longitude</label>
                                                            <input value="{{ $shop->longitude }}"   name="longitude" readonly id="lon" type="text" class="form-control" placeholder="Longitude"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 col-12">
                                                        <div class="form-group">
                                                            <label>Radius</label>
                                                            <input value="{{ $shop->location_radius }}"  name="location_radius" value="" type="text" class="form-control" placeholder="Radius"/>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-3 col-12">
                                                        <div class="form-group">
                                                            <label>Distributor</label>
                                                            <select onchange="get_tso()" name="distributor_id" id="distribuotr_id" class="select2 form-control form-control-lg">
                                                                <option value="">select</option>
                                                                @foreach ( $master->get_all_distributors() as $row )
                                                                 <option @if($shop->distributor_id==$row->id) selected @endif  value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                                                                @endforeach

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 col-12">
                                                        <div class="form-group">
                                                            <label>TSO</label>
                                                            <select onchange="get_route_by_tso()" name="tso_id" id="tso_id" class="select2 form-control form-control-lg">

                                                              @foreach ( $master->get_all_tso_by_distributor_id($shop->distributor_id) as $row )
                                                                <option @if($shop->tso_id==$row->id) selected @endif value="{{ $row->id }}">{{ $row->name }}</option>
                                                              @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 col-12">
                                                        <div class="form-group">
                                                            <label>Route</label>
                                                            <select onchange="get_sub_routes()" id="route_id" name="route_id" class="select2 form-control form-control-lg">
                                                              @foreach ( $master->get_all_route_by_tso($shop->tso_id) as $row )
                                                                <option @if($shop->route_id==$row->id) selected @endif value="{{ $row->id }}">{{ $row->route_name }}</option>
                                                              @endforeach


                                                            </select>
                                                        </div>
                                                    </div>


                                                    <div class="col-md-3 col-12">
                                                        <div class="form-group">
                                                            <label>Sub Routes</label>
                                                            <select  id="sub_route_id" name="sub_route_id" class="select2 form-control form-control-lg">
                                                                <option>Select</option>
                                                                @foreach ( $master->get_all_sub_routes_by_route($shop->route_id) as $row )
                                                                <option @if($shop->sub_route_id==$row->id) selected @endif value="{{ $row->id }}">{{ $row->name }}</option>
                                                              @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-primary mr-1">Submit</button>
                                                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 seprator">
                                                    <hr>
                                                </div>
                                                <div style="display: none">
                                                <div class="col-md-12">
                                                    <h4 class="card-title">Address Details</h4>
                                                </div>

                                                <div class="col-md-4 col-12">
                                                    <div class="form-group">
                                                        <label>Address</label>
                                                        <input name="" type="text" class="form-control" placeholder="Address" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-12">
                                                    <div class="form-group">
                                                        <label>City</label>
                                                        <input name="" type="text" class="form-control" placeholder="City" />
                                                    </div>
                                                </div>

                                                <div class="col-md-4 col-12">
                                                    <div class="form-group">
                                                        <label>Warehouse</label>
                                                        <select name="" class="select2 form-control form-control-lg">
                                                            <option value="option">Option</option>
                                                            <option value="option">Option</option>
                                                        </select>
                                                    </div>
                                                </div>



                                                <div class="col-md-12 seprator">
                                                    <hr>
                                                </div>

                                                <div style="display: none">
                                                <div class="col-md-12">
                                                    <h4 class="card-title">Bank Details</h4>
                                                </div>

                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Account No</label>
                                                        <input name="" type="text" class="form-control" placeholder="Account No" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Account Title</label>
                                                        <input name="" type="text" class="form-control" placeholder="Account Title" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Bank Name</label>
                                                        <input name="" type="text" class="form-control" placeholder="Bank Name" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-12">
                                                    <div class="form-group">
                                                        <label>Warehouse</label>
                                                        <select name="" class="select2 form-control form-control-lg">
                                                            <option value="option">Option</option>
                                                            <option value="option">Option</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
    <!-- Basic Floating Label Form section end -->

    <script>
        var latitude = parseFloat({{ isset($shop->latitude)?$shop->latitude:24.8607343}}); // Example latitude
        var longitude = parseFloat({{ isset($shop->longitude)?$shop->longitude:67.0011364}}); // Example longitude

        $(doucument).ready(function(){
            shopLocation();
        })
        function shopLocation(){
            console.log($('#shop_location').prop('checked'));
            var checked = $('#shop_location').prop('checked');
            if (checked) {
                $('.get_location').show();
                $('#lat').attr("required", true);
                $('#lon').attr("required", true);
                $('#radius').attr("required", true);
            }
            else{
                $('.get_location').hide();
                $('#lat').attr("required", false);
                $('#lon').attr("required", false);
                $('#radius').attr("required", false);
            }
        };
    </script>
@endsection
