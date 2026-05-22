@extends('layouts.master')
@section('title', 'Route Map & Shop Tracking')

@section('content')
<style>
    #map { height: 600px; width: 100%; margin-top: 20px; z-index: 1;}
    .leaflet-popup-content-wrapper { border-radius: 8px; }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Route Map & Shop Tracking</h4>
            </div>
            <div class="card-body">
                <form id="filterForm">
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label>Distributor</label>
                            <select name="distributor_id" id="distribuotr_id" class="form-control select2" onchange="get_tso()" required>
                                <option value="">Select Distributor</option>
                                @foreach($distributors as $distributor)
                                    <option value="{{ $distributor->id }}">{{ $distributor->distributor_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>TSO</label>
                            <select name="tso_id" id="tso_id" class="form-control select2" onchange="get_route_by_tso()" required>
                                <option value="">Select TSO</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Route</label>
                            <select name="route_id" id="route_id" class="form-control select2" required>
                                <option value="">Select Route</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group" style="margin-top: 25px;">
                            <button type="button" id="loadMapBtn" class="btn btn-primary btn-block">Load Route Map</button>
                        </div>
                    </div>
                    <!-- Hidden inputs for lat long -->
                    <input type="hidden" name="lat" id="current_lat">
                    <input type="hidden" name="lng" id="current_lng">
                </form>
                
                <div id="map"></div>

                {{-- Missing Lat/Long Shops Warning --}}
                <div id="missing-shops-section" style="display:none; margin-top:20px;">
                    <div class="alert alert-warning p-2">
                        <h5 class="mb-1"><i class="fas fa-exclamation-triangle"></i> Shops with Missing Coordinates</h5>
                        <p class="mb-2">The following shops could not be plotted on the map because their latitude/longitude is not set:</p>
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Shop Name</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="missing-shops-body"></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    var map;
    var markers = [];
    var routeLayer = null;

    $(document).ready(function() {
        if ($('.select2').length > 0) {
            $('.select2').select2();
        }

        // Get Current Location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                $('#current_lat').val(position.coords.latitude);
                $('#current_lng').val(position.coords.longitude);
            }, function(error) {
                console.log("Geolocation error: " + error.message);
                $('#current_lat').val(24.8607);
                $('#current_lng').val(67.0011);
            });
        } else {
             // Fallback
             $('#current_lat').val(24.8607);
             $('#current_lng').val(67.0011);
        }

        // Initialize empty map
        map = L.map('map').setView([24.8607, 67.0011], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        $('#loadMapBtn').click(function() {
            var route_id = $('#route_id').val();
            var lat = $('#current_lat').val();
            var lng = $('#current_lng').val();

            if (!route_id) {
                alert('Please select a route');
                return;
            }

            // Clear previous markers and lines
            for (var i = 0; i < markers.length; i++) {
                map.removeLayer(markers[i]);
            }
            markers = [];
            if (routeLayer) {
                map.removeLayer(routeLayer);
                routeLayer = null;
            }

            $.ajax({
                url: "{{ url('api/v1/route/nearby-shops') }}",
                type: "GET",
                data: { route_id: route_id, lat: lat, lng: lng },
                beforeSend: function() {
                    $('#loader').show();
                    $('#missing-shops-section').hide();
                    $('#missing-shops-body').empty();
                },
                success: function(res) {
                    $('#loader').hide();
                    if (res.success && res.data.length > 0) {
                        var latlngs = [];
                        var missingShops = [];

                        // Start location marker
                        var startMarker = L.marker([lat, lng], {
                            icon: L.icon({
                                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                                iconSize: [25, 41],
                                iconAnchor: [12, 41],
                                popupAnchor: [1, -34],
                                shadowSize: [41, 41]
                            })
                        }).addTo(map).bindPopup("<b>Your Current Location</b><br>Start Point");
                        markers.push(startMarker);
                        latlngs.push([lat, lng]);

                        $.each(res.data, function(key, shop) {
                            // Shop has missing coordinates (status = 0) — collect for warning table
                            if (shop.status === 0) {
                                missingShops.push(shop);
                                return;
                            }

                            var mlat = parseFloat(shop.latitude);
                            var mlng = parseFloat(shop.longitude);

                            if (!isNaN(mlat) && !isNaN(mlng)) {
                                latlngs.push([mlat, mlng]);

                                var customIcon = L.divIcon({
                                    className: 'custom-div-icon',
                                    html: "<div style='background-color:#3388ff;color:white;text-align:center;border-radius:50%;width:28px;height:28px;line-height:28px;font-size:12px;font-weight:bold;border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,0.4);'>" + shop.sequence + "</div>",
                                    iconSize: [28, 28],
                                    iconAnchor: [14, 14]
                                });

                                var marker = L.marker([mlat, mlng], {icon: customIcon}).addTo(map)
                                    .bindPopup(
                                        "<b>" + shop.company_name + "</b><br>" +
                                        "<span style='color:#3388ff;font-weight:bold;'>" + shop.sequence_text + "</span><br>" +
                                        "Distance: " + parseFloat(shop.distance).toFixed(2) + " km"
                                    );
                                markers.push(marker);
                            }
                        });

                        // Draw road route using OSRM
                        if (latlngs.length > 1) {
                            drawRoadRoute(latlngs);
                        }

                        // Show missing shops warning table
                        if (missingShops.length > 0) {
                            var rows = '';
                            $.each(missingShops, function(i, shop) {
                                rows += '<tr>' +
                                    '<td>' + (i + 1) + '</td>' +
                                    '<td>' + shop.company_name + '</td>' +
                                    '<td><span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> lat long missing of that shop</span></td>' +
                                    '</tr>';
                            });
                            $('#missing-shops-body').html(rows);
                            $('#missing-shops-section').show();
                        }

                    } else {
                        alert("No shops found for this route.");
                    }
                },
                error: function() {
                    $('#loader').hide();
                    alert("Error loading map data.");
                }
            });
        });
    });
    function drawRoadRoute(latlngs) {
        // OSRM expects coordinates as lon,lat pairs separated by semicolons
        var waypoints = latlngs.map(function(ll) {
            return ll[1] + ',' + ll[0]; // lon,lat
        }).join(';');

        var osrmUrl = 'https://router.project-osrm.org/route/v1/driving/' + waypoints
            + '?overview=full&geometries=geojson';

        $('#loader').show();

        $.ajax({
            url: osrmUrl,
            type: 'GET',
            success: function(data) {
                $('#loader').hide();
                if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                    var coords = data.routes[0].geometry.coordinates;

                    // Convert from [lon, lat] (GeoJSON) to [lat, lon] (Leaflet)
                    var leafletCoords = coords.map(function(c) {
                        return [c[1], c[0]];
                    });

                    routeLayer = L.polyline(leafletCoords, {
                        color: '#3388ff',
                        weight: 5,
                        opacity: 0.8
                    }).addTo(map);

                    map.fitBounds(routeLayer.getBounds(), { padding: [50, 50] });
                } else {
                    // Fallback: draw straight line if OSRM fails
                    routeLayer = L.polyline(latlngs, {
                        color: '#3388ff',
                        weight: 4,
                        opacity: 0.7,
                        dashArray: '10, 10'
                    }).addTo(map);
                    map.fitBounds(routeLayer.getBounds(), { padding: [50, 50] });
                }
            },
            error: function() {
                $('#loader').hide();
                // Fallback: draw straight line if OSRM unreachable
                routeLayer = L.polyline(latlngs, {
                    color: '#3388ff',
                    weight: 4,
                    opacity: 0.7,
                    dashArray: '10, 10'
                }).addTo(map);
                map.fitBounds(routeLayer.getBounds(), { padding: [50, 50] });
            }
        });
    }
</script>
@endsection
