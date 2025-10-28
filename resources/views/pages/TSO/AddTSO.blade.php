<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>
@extends('layouts.master')
@section('title', "SND || ADD NEW Order Booker")
@section('content')


<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">ADD NEW Order Booker</h4>
                </div>
                <div class="card-body">
                    <form id="subm" method="POST" action="{{ route('tso.store') }}" class="form">
                        @csrf
                        <div class="row">
                            <div class="col-md-2">
                                <div class="main_head">
                                    <h2>Order Booker Details</h2>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label>Order Booker Code <strong>*</strong></label>
                                            <input readonly type="text" id="unique_code" value="{{ $tso_code }}" class="form-control" placeholder="Product Code"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label>Name <strong>*</strong></label>
                                            <input name="name" type="text" class="form-control" placeholder="Order Booker Name"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label>Company Name <strong>*</strong></label>
                                            <input name="company_name" type="text" class="form-control" placeholder="Company Name"/>
                                        </div>
                                    </div>


                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label>Employee ID <strong>*</strong></label>
                                            <input name="emp_id" type="text" class="form-control" placeholder="Employee ID"/>
                                        </div>
                                    </div>


                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label>Email <strong>*</strong></label>
                                            <input type="email" name="email" class="form-control" placeholder="info@email.com">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label>Phone <strong>*</strong></label>
                                            <input type="text" name="phone" class="form-control" placeholder="03331231231">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label>Cell Phone</label>
                                            <input type="text" name="cell_phone" class="form-control" placeholder="03331231231">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label>Alt. Phone</label>
                                            <input type="text" name="alt_phone" class="form-control" placeholder="03331231231">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label>CNIC</label>
                                            <input type="text" name="cnic" class="form-control" placeholder="CNIC No ">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label>Address</label>
                                            <input type="text" name="address" class="form-control" placeholder="abc street, abc, abc">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label>City<strong>*</strong></label>
                                            <select name="city" class="form-control">
                                                <option value="">Select Option</option>
                                                @foreach($cities as $city)
                                                <option value="{{$city->id}}">{{$city->name}}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label>State<strong>*</strong></label>
                                            <input type="text" name="state" class="form-control" placeholder="Sindh">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label>Country</label>
                                            <input type="text" name="country" class="form-control" placeholder="Pakistan">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label>Notes</label>
                                            <input type="text" name="notes" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label>Photo</label>
                                            <input type="file" name="image_path" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label>Login</label>
                                            <input type="text" name="user_name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input type="password" name="password" class="form-control">
                                        </div>
                                    </div>




                                    <div class="col-md-3 col-12 mb-1 form-check">
                                        {{-- <strong>Distributor</strong>
                                         @foreach ($master->get_all_distributors() as $key => $row )
                                            <div class="form-check">
                                                <input class="form-check-input" value="{{ $row->id }}" type="checkbox" id="distributor{{ $row->id }}" name="distributor[]">
                                                <label class="form-check-label" for="distributor{{ $row->id }}">{{ $row->distributor_name }}</label>
                                            </div>
                                        @endforeach --}}

                                        <div class="form-group">
                                            <label>Distributor</label>
                                            {{-- <input type="password" name="password" class="form-control" placeholder="*********" /> --}}
                                            <select name="distributor[]" class="form-control" id="distributor" multiple>
                                                @foreach ($master->get_all_distributors() as $key => $row)
                                                    <option value="{{ $row->id }}"> {{ $row->distributor_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-12 mb-1">
                                        <div class="form-group">
                                            <label>User Role</label>
                                            <select name="role" class="select2 form-control form-control-lg">
                                                <option value="">Select Role</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12 mb-1">
                                        <div class="form-group">
                                            <label>Manager</label>
                                            <select name="manager" class="select2 form-control form-control-lg">
                                                <option value="">Select Manager</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12 mb-1">
                                        <div class="form-group">
                                            <label>KPO</label>
                                            <select name="kpo" class="select2 form-control form-control-lg">
                                                <option value="">Select KPO</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12 mb-1">
                                        <div class="form-group">
                                            <label>KPO # 2</label>
                                            <select name="kpo_2" class="select2 form-control form-control-lg">
                                                <option value="">Select KPO 2</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12 mb-1">
                                        <div class="form-group">
                                            <label>KPO # 3</label>
                                            <select name="kpo_3" class="select2 form-control form-control-lg">
                                                <option value="">Select KPO 3</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12 mb-1">
                                        <div class="form-group">
                                            <label>Department</label>
                                            <select name="department_id" class="select2 form-control form-control-lg">
                                                @foreach ($departments as $department)
                                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12 mb-1">
                                        <div class="form-group">
                                            <label>Designation</label>
                                            <select name="designation_id" class="select2 form-control form-control-lg">
                                                @foreach ($designations as $designation)
                                                    <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12 mb-1">
                                        <div class="form-group">
                                            <label>Spot Sale</label>
                                            <select name="spot_sale" class="select2 form-control form-control-lg">
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12 mb-1">
                                        <div class="form-group">
                                            <label>Auto Payment</label>
                                            <select name="auto_payment" class="select2 form-control form-control-lg">
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12 mb-1">
                                        <div class="form-group">
                                            <label>Geography</label>
                                            <select name="geography_id" class="select2 form-control form-control-lg">
                                                <option value="1">Karachi</option>
                                                <option value="2">Islamabad</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12 mb-1">
                                        <div class="form-group">
                                            <label>Date Of Join</label>
                                            <input type="date" class="form-control"  name="date_of_join">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12 mb-1">
                                        <div class="form-group">
                                            <label>Date of Leaving</label>
                                            <input type="date" class="form-control"  name="date_of_leaving">
                                        </div>
                                    </div>
                                </div>
                              <div class="row">
    <div class="col-md-3 col-12">
        <div class="form-group">
            <label class="control-label" for="shop_location">Location</label>
            <input type="checkbox" name="shop_location" id="shop_location" onclick="shopLocation()" value="1">
        </div>
    </div>
</div>

<div class="row get_location" style="display: none;">
    <div class="col-md-12" id="locations_wrapper">

      <div class="mb-2 ">
    <button type="button" class="btn btn-success btn-sm add_more d-flex justify-content-center">+ Add More</button>
</div>


        {{-- First Location Section --}}
        <div class="location_section border p-2 mb-3 rounded">
            <input type="text" name="map[]" class="form-control mb-2 search-input" placeholder="Search location"/>

            <div class="map" style="height: 300px; border:1px solid #ddd;"></div>

            <table class="table mt-2">
                <thead>
                    <tr>
                        <th>Location Title</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Radius (KM)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" class="form-control" name="location_name[]" placeholder="Location Title"/></td>
                        <td><input type="number" step="any" readonly class="form-control lat" name="latitude[]" placeholder="Latitude"/></td>
                        <td><input type="number" step="any" readonly class="form-control lon" name="longitude[]" placeholder="Longitude"/></td>
                        <td><input type="number" step="any" class="form-control" name="radius[]" placeholder="Radius (KM)"/></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove_section">X</button></td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>

                           
                        </div>
                         <div class="col-md-12 seprator">
                                <hr>
                            </div>

                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary mr-1">Create Order Booker</button>
                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
     
    @endsection
    @section('script')
    <script>
        var latitude = 24.8607343; // Example latitude
        var longitude = 67.0011364; // Example longitude
        //  var latitude = parseFloat({{ isset($shop->latitude)?$shop->latitude:24.8607343}}); // Example latitude
        //  var longitude = parseFloat({{ isset($shop->latitude)?$shop->latitude:67.0011364}}); // Example longitude
    $(document).ready(function() {
            $('#distributor').select2();
            $('#user_category').select2();
        });

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script>
    <script>
        function shopLocation() {
            var checked = $('#shop_location').prop('checked');
            if (checked) {
                $('.get_location').show();
                initMaps();
            } else {
                $('.get_location').hide();
            }
        }

        // Initialize Maps + Autocomplete
        // function initMaps() {
        //     $('.location_section').each(function(index, section) {
        //         let $mapEl = $(section).find('.map');
        //         if ($mapEl.data('map-initialized')) return;

        //         let map = new google.maps.Map($mapEl[0], {
        //             center: {lat: 24.8607, lng: 67.0011}, // Default Karachi
        //             zoom: 12
        //         });

        //         let marker = new google.maps.Marker({
        //             position: {lat: 24.8607, lng: 67.0011},
        //             map: map,
        //             draggable: true
        //         });

        //         let latInput = $(section).find('.lat');
        //         let lonInput = $(section).find('.lon');
        //         let searchInput = $(section).find('.search-input')[0];

        //         // Default set
        //         latInput.val(marker.getPosition().lat());
        //         lonInput.val(marker.getPosition().lng());

        //         // Drag event
        //         marker.addListener('dragend', function(e) {
        //             latInput.val(e.latLng.lat());
        //             lonInput.val(e.latLng.lng());
        //         });

        //         // Map click event
        //         map.addListener('click', function(e) {
        //             marker.setPosition(e.latLng);
        //             latInput.val(e.latLng.lat());
        //             lonInput.val(e.latLng.lng());
        //         });

        //         // Places Autocomplete
        //         let autocomplete = new google.maps.places.Autocomplete(searchInput);
        //         autocomplete.bindTo("bounds", map);

        //         autocomplete.addListener("place_changed", function () {
        //             let place = autocomplete.getPlace();
        //             if (!place.geometry || !place.geometry.location) return;

        //             map.setCenter(place.geometry.location);
        //             map.setZoom(15);

        //             marker.setPosition(place.geometry.location);
        //             latInput.val(place.geometry.location.lat());
        //             lonInput.val(place.geometry.location.lng());
        //         });

        //         // Mark initialized
        //         $mapEl.data('map-initialized', true);
        //     });
        // }

        
    function initMaps() {
    $('.location_section').each(function(index, section) {
        let $mapEl = $(section).find('.map');
        if ($mapEl.data('map-initialized')) return;

        // Get existing lat/lng if available
        let latInput = $(section).find('.lat');
        let lonInput = $(section).find('.lon');

        let lat = parseFloat(latInput.val()) || 24.8607; // fallback Karachi
        let lng = parseFloat(lonInput.val()) || 67.0011;

        let map = new google.maps.Map($mapEl[0], {
            center: {lat: lat, lng: lng},
            zoom: 12
        });

        let marker = new google.maps.Marker({
            position: {lat: lat, lng: lng},
            map: map,
            draggable: true
        });

        // Set inputs if empty
        if (!latInput.val()) latInput.val(lat);
        if (!lonInput.val()) lonInput.val(lng);

        // Marker drag event
        marker.addListener('dragend', function(e) {
            latInput.val(e.latLng.lat());
            lonInput.val(e.latLng.lng());
        });

        // Map click
        map.addListener('click', function(e) {
            marker.setPosition(e.latLng);
            latInput.val(e.latLng.lat());
            lonInput.val(e.latLng.lng());
        });

        // Autocomplete
        let searchInput = $(section).find('.search-input')[0];
        let autocomplete = new google.maps.places.Autocomplete(searchInput);
        autocomplete.bindTo("bounds", map);
        autocomplete.addListener("place_changed", function () {
            let place = autocomplete.getPlace();
            if (!place.geometry || !place.geometry.location) return;

            map.setCenter(place.geometry.location);
            map.setZoom(15);
            marker.setPosition(place.geometry.location);
            latInput.val(place.geometry.location.lat());
            lonInput.val(place.geometry.location.lng());
        });

        $mapEl.data('map-initialized', true);
    });
}


        // ✅ Add More Section
        $(document).on('click', '.add_more', function() {
            let newSection = `
            <div class="location_section border p-2 mb-3 rounded">
                <input type="text" name="map[]" class="form-control mb-2 search-input" placeholder="Search location"/>
                <div class="map" style="height: 300px; border:1px solid #ddd;"></div>
                <table class="table mt-2">
                    <thead>
                        <tr>
                            <th>Location Title</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Radius (KM)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" class="form-control" name="location_name[]" placeholder="Location Title"/></td>
                            <td><input type="number" step="any" readonly class="form-control lat" name="latitude[]" placeholder="Latitude"/></td>
                            <td><input type="number" step="any" readonly class="form-control lon" name="longitude[]" placeholder="Longitude"/></td>
                            <td><input type="number" step="any" class="form-control" name="radius[]" placeholder="Radius (KM)"/></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove_section">X</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            `;
            $('#locations_wrapper').append(newSection);
            initMaps(); // ✅ ensure new map initializes
        });

        // ✅ Remove Section
        $(document).on('click', '.remove_section', function() {
            $(this).closest('.location_section').remove();
        });
    </script>

    @endsection
