<?php
use
 App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>
@extends('layouts.master')
@section('title', 'Edit Distributor')
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit DISTRIBUTOR</h4>
                    </div>
                    <div class="card-body">
                        <form class="form" method="POST" id="subm" action="{{ route('distributor.update',$distributor->id) }}">
                            @method('PUT')
                            @csrf
                            <div class="row">
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label>Distributor Code</label>
                                        <input type="text" class="form-control" placeholder="Distributor Code"
                                            name="distributor_code" value="{{$distributor->distributor_code}}" />
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label>Custom Code (Optional)</label>
                                        <input type="text" class="form-control" placeholder="Custom Code"
                                            name="custom_code" value="{{$distributor->custom_code}}"/>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label>Contact Person</label>
                                        <input type="text" class="form-control" placeholder="Contact Person"
                                            name="contact_person" value="{{$distributor->contact_person}}"/>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label>Distributor Name</label>
                                        <input type="text" class="form-control" placeholder="Distributor Name"
                                            name="distributor_name" value="{{$distributor->distributor_name}}"/>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control" placeholder="Email" name="email" value="{{$distributor->email}}"/>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label>Phone #</label>
                                        <input type="number" class="form-control" placeholder="Phone #" name="phone" value="{{$distributor->phone}}"/>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label>Alt. Phone</label>
                                        <input type="number" class="form-control" placeholder="Alt. Phone"
                                            name="alt_phone" value="{{$distributor->alt_phone}}"/>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    {{-- <div class="form-group">
                                        <label>City</label>
                                        <input type="text" class="form-control" placeholder="City" name="city" value="{{$distributor->city}}"/>
                                    </div> --}}
                                    <div class="form-group">
                                        <label>City </label>
                                        <select required class="form-control" name="city_id" id="" required>
                                            <option value="">select</option>
                                            @foreach ($master->cities() as $row)
                                                <option value="{{ $row->id }}" {{$distributor->city_id == $row->id ? 'selected' : ''}}>{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label>State</label>
                                        <input type="text" class="form-control" placeholder="State" name="" value="{{$distributor->State}}"/>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label>ZIP</label>
                                        <input type="text" class="form-control" placeholder="ZIP" name="zip" value="{{$distributor->zip}}"/>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-group">
                                        <label>Zone</label>
                                        <Select class="form-control" name="zone_id">
                                            <option value="">Select</option>
                                            @foreach ($zones as $zone)
                                                <option value="{{ $zone->id }}" {{ ($zone->id == $distributor->zone_id)? 'selected ' : '' }}>{{ $zone->zone_name }}</option>
                                            @endforeach
                                        </Select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-6">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <textarea class="form-control" placeholder="Address" name="address">{{$distributor->address}}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6 col-6">
                                    <div class="form-group">
                                        <label>Notes</label>
                                        <textarea class="form-control" placeholder="Notes" name="note">{{$distributor->note}}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-12 seprator">
                                    <hr>
                                </div>

                                <div class="col-md-12">
                                    <h4 class="card-title">Pricing Type</h4>
                                </div>
                                @foreach ($priceTypes as $priceType)
                                    <div class="form-group ml-1">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="pricing_type_id[]"
                                            {{ in_array($priceType->id,  $distributor->price_types->pluck('id')->toArray()) ? 'checked' : '' }}
                                                id="pricing_type{{ $priceType->id }}" value="{{ $priceType->id }}" />
                                            <label
                                                for="pricing_type{{ $priceType->id }}">{{ $priceType->price_type_name }}</label>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="col-md-12">
                                    <h4 class="card-title">Discount Slab</h4>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Min Discount (%)</label>
                                        <input type="number" step="0.01" class="form-control"
                                            placeholder="Min Discount (%)" name="min_discount" value="{{$distributor->min_discount}}"/>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Max Discount (%)</label>
                                        <input type="number" step="0.01" class="form-control"
                                            placeholder="Max Discount (%)" name="max_discount" value="{{$distributor->max_discount}}"/>
                                    </div>
                                </div>
                                <div class="col-md-12 map-tb">
                                    <input type="text" name="map" value="{{$distributor->map}}" class="form-control controls" placeholder="Map name" id="pac-input"/>

                                    <div id="map"></div>
                                </div>
                                <div class="col-12">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Location Title</th>
                                                <th>Latitude</th>
                                                <th>Longitude</th>
                                                <th>Radius (KM)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><input type="text" class="form-control"
                                                        placeholder="Location Title" name="location_title" value="{{$distributor->location_title}}"/></td>
                                                <td><input type="number" step="any" class="form-control"
                                                        placeholder="Latitude" name="location_latitude" id="lat" readonly value="{{$distributor->location_latitude}}"/></td>
                                                <td><input type="number" step="any" class="form-control"
                                                        placeholder="Longitude" name="location_longitude" id="lon" readonly value="{{$distributor->location_longitude}}"/></td>
                                                <td><input type="number" step="any" class="form-control"
                                                        placeholder="Radius (KM)" name="location_radius" value="{{$distributor->location_radius}}"/></td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-12">
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

@endsection
@section('script')
<script>
    var latitude = parseFloat({{ isset($distributor->latitude)?$distributor->latitude:24.8607343}}); // Example latitude
    var longitude = parseFloat({{ isset($distributor->longitude)?$distributor->longitude:67.0011364}}); // Example longitude


</script>
@endsection
