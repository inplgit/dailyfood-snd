@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
@endphp
@extends('layouts.master')
@section('title', 'TSO Activity')
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">TSO Activity</h4>
                    </div>
                    <div class="card-body">
                        <form method="get" action="{{ route('activity') }}" id="list_data" class="form">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>City </label>
                                        <select required class="form-control" name="city" id="city" onchange="getDistributorByCity()" >
                                            <option value="">select</option>
                                            @foreach ($master->cities() as $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Distributor Name* </label>
                                        <select required onchange="get_tso()" class="form-control" name="distributor_id"
                                            id="distribuotr_id" required>
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
                                        <select class="form-control" id="tso_id" name="tso_id" required>
                                            <option value="">select</option>
                                            {{-- @foreach ($master->get_all_tso() as $row)
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Date</label>
                                        <input type="date" name="date" id="date" class="form-control" value="{{date('Y-m-d')}}">
                                    </div>
                                </div>
                                <div class="col-md-3 mt-2">
                                    <button onclick="get_ajax_data()" type="button"
                                        class="btn btn-primary mr-1">Generate</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 map-tb">
            {{-- <input type="text" name="map" class="form-control controls" placeholder="Map name" id="pac-input"/> --}}

            <div id="map"></div>
        </div>
       <div id="data"></div>



    </section>
    <!-- Basic Floating Label Form section end -->
@endsection
@section('script')
<script>
    // function get_distributor_by_city(){
        //     var city = $('#city').val();
        //     var o = new Option("select", "");
        //     $("#distribuotr_id").html(o);


        //         $.ajax({
        //             type: "get",
        //             url: '{{ route('route.get_distributor_by_city') }}',
        //             data: {
        //                 city: city
        //             },
        //             async: true,
        //             cache: false,
        //             success: function(data) {
        //                 // $('#data').html(data);
        //                 $.each(data.distributor, function(key, value) {

        //                 $('#distribuotr_id').append($('<option>', {
        //                     value: value.id,
        //                     text: value.distributor_name
        //                 }));
        //                 });

        //             }
        //         });
        // }
    $(document).ready(function() {
        $('#distribuotr_id').select2();
        $('#city').select2();
        $('#tso_id').select2();
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@endsection
