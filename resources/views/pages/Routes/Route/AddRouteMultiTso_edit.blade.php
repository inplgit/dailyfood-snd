<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>
@extends('layouts.master')
@section('title', "SND || Edit Route")
@section('content')

<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Route</h4>
                </div>
                <div class="card-body">
                    <form method="post"  action="{{ route('AddRouteMultiTso_update', $route->id) }}">
                       @csrf
    @method('PUT')   {{-- or 'PATCH' --}}
                    

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Route Name</label>
                                    <input type="text" name="route_name" value="{{ $route->route_name }}" class="form-control" required />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Distributor Name</label>
                                    <select name="distributor_id" id="distribuotr_id" class="form-control" onchange="get_tso()" required>
                                        <option value="">select</option>
                                        @foreach ($master->get_all_distributor_user_wise() as $row)
                                            <option value="{{ $row->id }}" {{ $route->distributor_id == $row->id ? 'selected' : '' }}>
                                                {{ $row->distributor_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>TSO Name</label>
                                    <select name="tso_id[]" id="tso_id" class="form-control tso-id" multiple>
                                        @foreach ($distributor_tso as $tso)
                                            <option value="{{ $tso->id }}" {{ in_array($tso->id, $route_tsos) ? 'selected' : '' }}>
                                                {{ $tso->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Days</label>
                                    <select name="day[]" id="day" class="form-control" multiple required>
                                        @foreach ($master->Days() as $day)
                                            <option value="{{ $day }}" {{ in_array($day, $route_day) ? 'selected' : '' }}>
                                                {{ $day }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12 mt-2 text-right">
                                <button type="submit" class="btn btn-primary">Update Route</button>
                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
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
    $(document).ready(function () {
        $('#day').select2();
        $('.tso-id').select2();
    });

    function get_tso() {
        const distributor_id = $('#distribuotr_id').val();
        const $tso = $('#tso_id');
        const selectedTsoIds = @json($route_tsos);

        $tso.html('');

        if (distributor_id) {
            $.ajax({
                type: "GET",
                url: '{{ route('route.GetTsoByDistributormulti') }}',
                data: { distributor_id },
                dataType: 'json',
                success: function (data) {
                    if (data.tso && data.tso.length > 0) {
                        data.tso.forEach((value) => {
                            const isSelected = selectedTsoIds.includes(value.id);
                            const option = new Option(value.name, value.id, false, isSelected);
                            $tso.append(option);
                        });
                        $tso.trigger('change');
                    } else {
                        alert('No TSOs found for the selected distributor.');
                    }
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                    alert('Failed to fetch TSOs.');
                }
            });
        }
    }
</script>
@endsection
