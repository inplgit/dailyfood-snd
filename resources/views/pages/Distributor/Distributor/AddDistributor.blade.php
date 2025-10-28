
<?php
use
 App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>
@extends('layouts.master')
@section('title', 'Add Distributor')
@section('content')


    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">ADD NEW DISTRIBUTOR</h4>
                    </div>
                    <div class="card-body">
                        <form class="form" method="POST" action="{{ route('distributor.store') }}" id="subm">
                            @csrf
                            <div class="row">

                                <div class="col-md-2">
                                    <div class="main_head">
                                        <h2>Distributor Details</h2>
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="row">

                                        <div class="col-md-12 col-12">
                                            <div class="form-group">
                                                <label class="control-label">Parent Distributor</label>
                                                <select class="form-control" name="parent_distributor">
                                                    <option value="0">Select</option>
                                                    @foreach ($master->get_distributor_level_wise() as $key => $row)
                                                    <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-12">
                                            <div class="form-group">
                                                <label class="control-label">Distributor Code</label>
                                                <input readonly value="{{ $unique_no }}" type="text" class="form-control" placeholder="Distributor Code" name="distributor_code" />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            <div class="form-group">
                                                <label class="control-label">Custom Code (Optional)</label>
                                                <input type="text" class="form-control" placeholder="Custom Code" name="custom_code" />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            <div class="form-group">
                                                <label class="control-label">Contact Person</label>
                                                <input type="text" class="form-control" placeholder="Contact Person" name="contact_person" />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            <div class="form-group">
                                                <label class="control-label">Distributor Name</label>
                                                <input type="text" class="form-control" placeholder="Distributor Name" name="distributor_name" />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            <div class="form-group">
                                                <label class="control-label">Email</label>
                                                <input type="email" class="form-control" placeholder="Email" name="email" />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            <div class="form-group">
                                                <label class="control-label">Phone #</label>
                                                <input type="number" class="form-control" placeholder="Phone #" name="phone" />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            <div class="form-group">
                                                <label class="control-label">Alt. Phone</label>
                                                <input type="number" class="form-control" placeholder="Alt. Phone"
                                                    name="alt_phone" />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            {{-- <div class="form-group">
                                                <label class="control-label">City</label>
                                                <input type="text" class="form-control" placeholder="City" name="city" />
                                            </div> --}}
                                            <div class="form-group">
                                                <label>City </label>
                                                <select required class="form-control" name="city_id" id="" required>
                                                    <option value="">select</option>
                                                    @foreach ($master->cities() as $row)
                                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            <div class="form-group">
                                                <label class="control-label">State</label>
                                                <input type="text" class="form-control" placeholder="State" name="" />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            <div class="form-group">
                                                <label class="control-label">ZIP</label>
                                                <input type="text" class="form-control" placeholder="ZIP" name="zip" />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-12">
                                            <div class="form-group">
                                                <label class="control-label">Zone</label>
                                                <Select class="form-control" name="zone_id">
                                                    <option value="">Select</option>
                                                    @foreach ($zones as $zone)
                                                        <option value="{{$zone->id}}">{{ $zone->zone_name }}</option>
                                                    @endforeach
                                                </Select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label class="control-label">Address</label>
                                                <textarea class="form-control" placeholder="Address" name="address"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <div class="form-group">
                                                <label class="control-label">Notes</label>
                                                <textarea class="form-control" placeholder="Notes" name="note"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 seprator">
                                    <hr>
                                </div>

                                <div class="col-md-2">
                                    <div class="main_head">
                                        <h2>Pricing Type</h2>
                                    </div>
                                </div>

                                <div class="col-md-10">
                                    @foreach ($priceTypes as $priceType)
                                        <div class="form-group ml-1">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="pricing_type_id[]" id="pricing_type{{$priceType->id}}" value="{{$priceType->id}}" />
                                                <label class="control-label" for="pricing_type{{$priceType->id}}">{{ $priceType->price_type_name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="col-md-12 seprator">
                                    <hr>
                                </div>

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="main_head">
                                            <h2>Discount Slab</h2>
                                        </div>
                                    </div>

                                    <div class="col-md-10">
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label class="control-label">Min Discount (%)</label>
                                                    <input type="number" step="0.01" class="form-control" placeholder="Min Discount (%)"
                                                        name="min_discount" />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label class="control-label">Max Discount (%)</label>
                                                    <input type="number" step="0.01" class="form-control" placeholder="Max Discount (%)"
                                                        name="max_discount" />
                                                </div>
                                            </div>
                                            <div class="col-md-12 map-tb">
                                                <input type="text" name="map" class="form-control controls" placeholder="Map name" id="pac-input"/>

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
                                                                    placeholder="Location Title" name="location_title" /></td>
                                                            <td><input type="number" step="any"  id="lat" readonly class="form-control" placeholder="Latitude"
                                                                    name="location_latitude" /></td>
                                                            <td><input type="number" step="any" id="lon" readonly class="form-control" placeholder="Longitude"
                                                                    name="location_longitude" /></td>
                                                            <td><input type="number" step="any" class="form-control" placeholder="Radius (KM)"
                                                                    name="location_radius" /></td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <!-- <div class="col-md-12">
                                    <h4 class="card-title">Pricing Type</h4>
                                </div> -->

                                <!-- <div class="col-md-12">
                                    <h4 class="card-title">Discount Slab</h4>
                                </div> -->
                                <div class="col-md-12 seprator">
                                    <hr>
                                </div>

                                <div class="col-md-12 text-right">
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
    var latitude = 24.8607343; // Example latitude
     var longitude = 67.0011364; // Example longitude
</script>
@endsection
