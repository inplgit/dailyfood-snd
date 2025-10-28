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
                                        <form id="subm" method="post" action="{{ route('shop.store') }}" class="form">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="main_head">
                                                        <h2>Shop Details</h2>
                                                    </div>
                                                </div>
                                                <div class="col-md-10">
                                                    <div class="row">
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Shop Code</label>
                                                                <input name="shop_code" value="{{ $uniqe_no }}" readonly type="text" val class="form-control" placeholder="Shop Code"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Custom Code (Optional)</label>
                                                                <input name="custome_code" type="text" class="form-control" placeholder="Custom Code"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Shop Name *</label>
                                                                <input name="company_name" type="text" class="form-control" placeholder="Company Name"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Title</label>
                                                                <input type="text" name="title" class="form-control" placeholder="Title">

                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Contact Person *</label>
                                                                <input name="contact_person" type="text" class="form-control" placeholder="Contact Person"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Email</label>
                                                                <input name="email" type="email" class="form-control" placeholder="Email"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Alt. Email</label>
                                                                <input name="alt_email" type="email" class="form-control" placeholder="Alt. Email"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Phone #</label>
                                                                <input name="phone" type="text" class="form-control" placeholder="Phone #"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Mobile No *</label>
                                                                <input name="mobile_no" type="text" class="form-control" placeholder="Mobile No."/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Alt. Phone</label>
                                                                <input name="alt_phone" type="text" class="form-control" placeholder="Alt. Phone"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Address</label>
                                                                <input name="address" type="text" class="form-control" placeholder="Address"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Address Line 2</label>
                                                                <input name="address_2" type="text" class="form-control" placeholder="Address Line 2"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">City *</label>

                                                                <select class="form-control" name="city" id="city">
                                                                    <option value="">Select</option>
                                                                    @foreach ($master->cities() as $row)
                                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                                    @endforeach

                                                                </select>

                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">State</label>
                                                                <input name="state" type="text" class="form-control" placeholder="State"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">ZIP</label>
                                                                <input name="zip_code" type="text" class="form-control" placeholder="ZIP"/>
                                                            </div>
                                                        </div>


                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Notes</label>
                                                                <textarea  name="note" class="form-control" placeholder="Notes"></textarea>
                                                            </div>
                                                        </div>


                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">This Shop is Tax Filer / Registered?</label>
                                                                <select name="filer" class="select2 form-control form-control-lg">
                                                                    <option value="1">Yes</option>
                                                                    <option value="0">No</option>
                                                                </select>
                                                            </div>
                                                        </div>



                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">CNIC</label>
                                                                <input name="cnic" type="text" class="form-control" placeholder="CNIC" />
                                                            </div>
                                                        </div>



                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Allowed Credit (No of Days)</label>
                                                                <input name="allow_credit_days" type="text" class="form-control" placeholder="Allowed Credit (No of Days)" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Allowed Credit (Amount)</label>
                                                                <input name="allow_credit_amount" type="text" class="form-control" placeholder="Allowed Credit (Amount)" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">No of delivery days</label>
                                                                <input name="delvery_days" type="text" class="form-control" placeholder="Allowed Credit (Amount)" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Invoice Discount.</label>
                                                                <input name="invoice_discount" type="text" class="form-control" placeholder="Allowed Credit (Amount)" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Shop Type</label>
                                                                <select name="shop_type_id" class="select2 form-control form-control-lg">
                                                                    <option value="">select</option>
                                                                    @foreach ( $master->get_all_shop_type() as $row )
                                                                    <option value="{{ $row->id }}">{{ $row->shop_type_name }}</option>
                                                                    @endforeach

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Shop Zone</label>
                                                                <select name="shop_zone_id" class="select2 form-control form-control-lg">
                                                                    <option value="">select</option>
                                                                    @foreach ($master->get_all_zone() as $row)
                                                                    <option value="{{ $row->id }}">{{ $row->zone_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label" >Opening Balance Amount.</label>
                                                                <input name="balance_amount" min="0" value="0" type="number" step="any" class="form-control" placeholder="Balance Amount" />
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Opening Balance Type.</label>
                                                                <select name="debit_credit"  class="form-control" id="">
                                                                    <option value="1">Debit</option>
                                                                    <option value="2">Credit</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label>Photo *</label>
                                                                <input type="file" name="image" class="form-control" accept="image/*">
                                                            </div>
                                                        </div>

                                                        {{-- <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Credit Limit</label>
                                                                <input name="credit_limit" type="number" class="form-control" placeholder="credit_limit"/>
                                                            </div>
                                                        </div> --}}
                                                        <div class="col-md-12 seprator">
                                                            <hr>
                                                        </div>
                                                        <div class="col-md-12 seprator">
                                                            <hr>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label" for="shop_location">Shop Location.</label>
                                                                <input type="checkbox"  name="shop_location" id="shop_location" onclick="shopLocation()" value="1">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row get_location" style="display: none;">

                                                        <div class="col-md-12 map-tb">
                                                            <input type="text" name="map" class="form-control controls" placeholder="Map name" id="pac-input"/>

                                                            <div id="map"></div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Latitude</label>
                                                                <input name="latitude" readonly id="lat" type="text" class="form-control" placeholder="Latitude"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Longitude</label>
                                                                <input name="longitude" readonly id="lon" type="text" class="form-control" placeholder="Longitude"/>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Radius</label>
                                                                <input name="location_radius" value="" id="radius" type="text" class="form-control" placeholder="Radius"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">

                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Distributor *</label>
                                                                <select onchange="get_tso()" name="distributor_id" id="distribuotr_id" class="select2 form-control form-control-lg">
                                                                    <option value="">select</option>
                                                                    @foreach ( $master->get_all_distributor_user_wise() as $row )
                                                                    <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                                                                    @endforeach

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Order Booker *</label>
                                                                <select onchange="get_route_by_tso()" name="tso_id" id="tso_id" class="select2 form-control form-control-lg">


                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Route *</label>
                                                                <select onchange="get_sub_routes()" id="route_id" name="route_id" class="select2 form-control form-control-lg">

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Sub Routes</label>
                                                                <select  id="sub_route_id" name="sub_route_id" class="select2 form-control form-control-lg">

                                                                </select>
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
                                                                <label class="control-label">Address</label>
                                                                <input name="" type="text" class="form-control" placeholder="Address" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">City</label>
                                                                <input name="" type="text" class="form-control" placeholder="City" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-12">
                                                            <div class="form-group">
                                                                <label class="control-label">Warehouse</label>
                                                                <select name="" class="select2 form-control form-control-lg">
                                                                    <option value="option">Option</option>
                                                                    <option value="option">Option</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                                <!-- <div class="col-md-12 seprator">
                                                    <hr>
                                                </div> -->

                                                <!-- <div style="display: none">
                                                    <div class="col-md-12">
                                                        <h4 class="card-title">Bank Details</h4>
                                                    </div>

                                                    <div class="col-md-3 col-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Account No</label>
                                                            <input name="" type="text" class="form-control" placeholder="Account No" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 col-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Account Title</label>
                                                            <input name="" type="text" class="form-control" placeholder="Account Title" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 col-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Bank Name</label>
                                                            <input name="" type="text" class="form-control" placeholder="Bank Name" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 col-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Warehouse</label>
                                                            <select name="" class="select2 form-control form-control-lg">
                                                                <option value="option">Option</option>
                                                                <option value="option">Option</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div> -->

                                                <div class="col-md-12 mt-4 text-right">
                                                    <button type="submit" class="btn btn-primary mr-1">Submit</button>
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

    <script>
        var latitude = parseFloat({{ isset($shop->latitude)?$shop->latitude:24.8607343}}); // Example latitude
        var longitude = parseFloat({{ isset($shop->longitude)?$shop->longitude:67.0011364}}); // Example longitude

        // $(document).ready(function(){
        // })
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
        }
    </script>
@endsection
