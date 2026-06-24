@extends('layouts.master')
@section('title', "SND || Edit Route Radius Configuration")
@section('content')

<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Route Radius Configuration</h4>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('route-radius-configurations.update', $configuration->id) }}" class="form">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Distributor</label>
                                    <select onchange="get_tso_route(this)" class="form-control" name="distributor_id" id="distributor_id" required>
                                        <option value="">Select Distributor</option>
                                        @foreach ($distributors as $row)
                                            <option value="{{ $row->id }}" {{ $configuration->distributor_id == $row->id ? 'selected' : '' }}>
                                                {{ $row->distributor_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">TSO</label>
                                    <select onchange="get_routes(this)" class="form-control tso-id" name="tso_id" id="tso_id" required>
                                        <option value="">Select TSO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Radius (meters)</label>
                                    <input type="number" name="radius" class="form-control" placeholder="e.g. 200" required min="1" step="0.01" value="{{ $configuration->radius }}" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Select Routes</label>
                                    <div class="mb-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="select-all-routes">Select All</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselect-all-routes">Deselect All</button>
                                    </div>
                                    <select class="form-control routes-select" name="route_ids[]" multiple="multiple" required>
                                        <option value="">Select TSO First</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 mt-2 text-right">
                                <button type="submit" class="btn btn-primary mr-1">Update Configuration</button>
                                <a href="{{ route('route-radius-configurations.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
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
    // Pre-selected values from the existing configuration
    const existingDistributorId = "{{ $configuration->distributor_id }}";
    const existingTsoId         = "{{ $configuration->tso_id }}";
    const existingRouteIds      = @json($configuration->routes->pluck('id'));

    $(document).ready(function () {
        $('.routes-select').select2({ placeholder: "Select Routes" });

        $('#select-all-routes').on('click', function () {
            $('.routes-select > option').prop("selected", true);
            $('.routes-select').trigger("change");
        });

        $('#deselect-all-routes').on('click', function () {
            $('.routes-select > option').prop("selected", false);
            $('.routes-select').trigger("change");
        });

        // Auto-load TSOs on page load for the saved distributor
        if (existingDistributorId) {
            loadTSOs(existingDistributorId, function () {
                // After TSOs are loaded, select the saved TSO and load its routes
                $('#tso_id').val(existingTsoId);
                if (existingTsoId) {
                    loadRoutes(existingTsoId, function () {
                        // Pre-select previously assigned routes
                        existingRouteIds.forEach(function (id) {
                            $('.routes-select option[value="' + id + '"]').prop('selected', true);
                        });
                        $('.routes-select').trigger('change');
                    });
                }
            });
        }
    });

    // Called when distributor dropdown changes manually
    function get_tso_route(element) {
        const distributorId = $(element).val();
        const tsoSelect = $('.tso-id');
        const routeSelect = $('.routes-select');

        tsoSelect.html('<option value="">Select TSO</option>');
        routeSelect.html('');
        routeSelect.trigger('change');

        if (distributorId) {
            loadTSOs(distributorId);
        }
    }

    // Called when TSO dropdown changes manually
    function get_routes(element) {
        const tsoId = $(element).val();
        const routeSelect = $('.routes-select');

        routeSelect.html('');
        routeSelect.trigger('change');

        if (tsoId) {
            loadRoutes(tsoId);
        }
    }

    // Helper: fetch TSOs for a distributor; optional callback on success
    function loadTSOs(distributorId, callback) {
        $.ajax({
            type: "GET",
            url: '{{ route('route.GetTsoByDistributormulti') }}',
            data: { distributor_id: distributorId },
            dataType: 'json',
            success: function (data) {
                const tsoSelect = $('.tso-id');
                tsoSelect.html('<option value="">Select TSO</option>');
                if (data.tso && data.tso.length > 0) {
                    data.tso.forEach(function (value) {
                        tsoSelect.append(new Option(value.name, value.id));
                    });
                } else {
                    alert('No TSOs found for the selected distributor.');
                }
                if (typeof callback === 'function') callback();
            },
            error: function () {
                alert('Failed to fetch TSO data.');
            }
        });
    }

    // Helper: fetch routes for a TSO; optional callback on success
    function loadRoutes(tsoId, callback) {
        $.ajax({
            type: "GET",
            url: '{{ route('route.GetRouteBYTSO') }}',
            data: { tso_id: tsoId },
            dataType: 'json',
            success: function (data) {
                const routeSelect = $('.routes-select');
                routeSelect.html('');
                if (data.route && data.route.length > 0) {
                    data.route.forEach(function (value) {
                        routeSelect.append(new Option(value.route_name, value.id));
                    });
                    routeSelect.trigger('change');
                } else {
                    alert('No routes found for the selected TSO.');
                }
                if (typeof callback === 'function') callback();
            },
            error: function () {
                alert('Failed to fetch route data.');
            }
        });
    }
</script>
@endsection
