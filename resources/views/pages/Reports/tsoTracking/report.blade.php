@php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
@endphp
@extends('layouts.master')
@section('title', 'TSO Tracking Report')
@section('disable_global_google_map', '1')

@section('css-end')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #map { height: 600px; width: 100%; border-radius: 8px; margin-top: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    /* Prevent global image CSS from breaking Leaflet tiles. */
    #map .leaflet-container img,
    #map .leaflet-pane img,
    #map img.leaflet-tile {
        max-width: none !important;
        max-height: none !important;
    }
    #map img.leaflet-tile {
        width: 256px !important;
        height: 256px !important;
    }
    .tracking-info { margin-top: 15px; }
    .marker-pin { width: 30px; height: 30px; border-radius: 50% 50% 50% 0; background: #c30b82; position: absolute; transform: rotate(-45deg); left: 50%; top: 50%; margin: -15px 0 0 -15px; }
    .marker-pin::after { content: ''; width: 24px; height: 24px; margin: 3px 0 0 3px; background: #fff; border-radius: 50%; position: absolute; }
</style>
@endsection

@section('content')
<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">TSO Tracking Report</h4>
                </div>
                <div class="card-body">
                    <form id="trackingForm" class="form">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <input type="date" name="from" id="from" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" name="to" id="to" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Distributor Name </label>
                                    <select onchange="get_tso_by_multiple_dis()" multiple class="select2 form-control" name="distributor_id[]" id="distribuotr_id">
                                        <option value="">select</option>
                                        @foreach ($master->get_all_distributor_user_wise() as $row)
                                            <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>TSO Name</label>
                                    <select class="form-control select2" multiple id="tso_id" name="tso_id[]" required>
                                        <option value="">select</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <button type="button" class="btn btn-primary mr-1" onclick="generateReport()">Generate Report</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="resetMap()">Reset Map</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div id="map"></div>
                    <div id="reportData" class="mt-4">
                        <!-- AJAX results will appear here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    var map;
    var routeLayers = [];
    var markers = [];
    var defaultCenter = [30.3753, 69.3451];
    var defaultZoom = 6;
    var routeColors = ['#c30b82', '#007bff', '#28a745', '#ff9800', '#9c27b0', '#009688'];

    $(document).ready(function() {
        $('.select2').select2();
        initMap();
    });

    function initMap() {
        map = L.map('map').setView(defaultCenter, defaultZoom);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Fix map rendering issues after dynamic content/layout changes.
        setTimeout(function () {
            map.invalidateSize();
        }, 200);
    }

    function generateReport() {
        let distributor_id = $('#distribuotr_id').val();
        let tso_id = $('#tso_id').val();
        
        if(!distributor_id) {
            alert('Please select a distributor');
            return;
        }

        $.ajax({
            url: "{{ route('report.tso_tracking_report') }}",
            type: "GET",
            data: $('#trackingForm').serialize(),
            beforeSend: function() {
                $('.btn-primary').prop('disabled', true).text('Generating...');
            },
            success: function(response) {
                $('.btn-primary').prop('disabled', false).text('Generate Report');
                $('#reportData').html(response);
            },
            error: function(xhr) {
                $('.btn-primary').prop('disabled', false).text('Generate Report');
                alert('Error generating report');
            }
        });
    }

    function resetMap() {
        routeLayers.forEach(layer => map.removeLayer(layer));
        markers.forEach(marker => map.removeLayer(marker));
        routeLayers = [];
        markers = [];
        map.setView(defaultCenter, defaultZoom);
    }

    function sampleWaypoints(routePoints, maxPoints) {
        if (routePoints.length <= maxPoints) {
            return routePoints;
        }

        let sampled = [];
        let step = (routePoints.length - 1) / (maxPoints - 1);
        for (let i = 0; i < maxPoints; i++) {
            sampled.push(routePoints[Math.round(i * step)]);
        }
        return sampled;
    }

    function fitMapToLayers() {
        let allLayers = routeLayers.concat(markers);
        if (!allLayers.length) return;

        let group = L.featureGroup(allLayers);
        map.invalidateSize();
        map.fitBounds(group.getBounds(), { padding: [30, 30] });
    }

    function drawRoadRoute(routePoints, color) {
        let sampledPoints = sampleWaypoints(routePoints, 20);
        let coordPath = sampledPoints.map(function (p) {
            return p.longitude + ',' + p.latitude;
        });

        if (coordPath.length < 2) {
            return;
        }

        let osrmUrl = 'https://router.project-osrm.org/route/v1/driving/' +
            coordPath.join(';') +
            '?overview=full&geometries=geojson';

        $.ajax({
            url: osrmUrl,
            type: 'GET',
            dataType: 'json'
        }).done(function (response) {
            if (!response || !response.routes || !response.routes.length || !response.routes[0].geometry) {
                throw new Error('No route geometry returned');
            }

            let routeCoords = response.routes[0].geometry.coordinates.map(function (coord) {
                return [coord[1], coord[0]];
            });

            let routeLine = L.polyline(routeCoords, { color: color, weight: 5, opacity: 0.8 }).addTo(map);
            routeLayers.push(routeLine);
            fitMapToLayers();
        }).fail(function () {
            let fallbackLatLngs = sampledPoints.map(function (p) {
                return [p.latitude, p.longitude];
            });
            let fallbackLine = L.polyline(fallbackLatLngs, { color: color, weight: 4, opacity: 0.6, dashArray: '6, 8' }).addTo(map);
            routeLayers.push(fallbackLine);
            fitMapToLayers();
        });
    }

    function drawPath(points) {
        resetMap();

        if (!Array.isArray(points) || points.length === 0) {
            alert('No tracking data found for the selected filters.');
            return;
        }

        let cleanedPoints = points
            .map(function (p) {
                return {
                    ...p,
                    latitude: parseFloat(p.latitude),
                    longitude: parseFloat(p.longitude)
                };
            })
            .filter(function (p) {
                return Number.isFinite(p.latitude) &&
                    Number.isFinite(p.longitude) &&
                    p.latitude >= -90 && p.latitude <= 90 &&
                    p.longitude >= -180 && p.longitude <= 180;
            })
            .sort(function (a, b) {
                return new Date(a.sync_date_time) - new Date(b.sync_date_time);
            });

        if (!cleanedPoints.length) {
            alert('Tracking data exists but coordinates are invalid.');
            return;
        }

        let grouped = {};
        cleanedPoints.forEach(function (p) {
            let key = (p.tso_id || 'unknown') + '_' + (p.user_id || 'unknown');
            if (!grouped[key]) grouped[key] = [];
            grouped[key].push(p);
        });

        Object.keys(grouped).forEach(function (key, index) {
            let routePoints = grouped[key];
            let latlngs = routePoints.map(p => [p.latitude, p.longitude]);
            let color = routeColors[index % routeColors.length];

            if (latlngs.length > 1) {
                drawRoadRoute(routePoints, color);
            }

            let first = routePoints[0];
            let last = routePoints[routePoints.length - 1];
            let startMarker = L.marker([first.latitude, first.longitude]).addTo(map)
                .bindPopup('<b>Start Point</b><br>' + (first.tso_name || '') + '<br>' + first.sync_date_time);
            let endMarker = L.marker([last.latitude, last.longitude]).addTo(map)
                .bindPopup('<b>End Point</b><br>' + (last.tso_name || '') + '<br>' + last.sync_date_time);

            markers.push(startMarker, endMarker);
        });

        fitMapToLayers();
    }
</script>
@endsection
