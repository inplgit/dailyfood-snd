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
                        <h4 class="card-title"> Item Wise Sale </h4>
                        <button type="button" id="exportBtn" onclick="exportBtn('Item Wise Sales')" class="btn btn-success">Export Excel</button>
                    </div>
                    <div class="card-body">
                        <form method="get" action="{{ route('item_wise_sale') }}" id="list_data" class="form">
                            @csrf
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>From</label>
                                        <input type="date"  name="from" id="date" class="form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>To</label>
                                        <input type="date"  name="to" id="date" class="form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Type</label>
                                        <select class="form-control" onchange="type_change()" id="type" name="type" required>
                                            <option value="Detail">Detail</option>
                                            <!-- <option value="Summary">Summary Stock</option> -->
                                            <option value="Summary_new_details">Summary</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="type_data">

                                <div class="col-md-4 city">
                                    <div class="form-group">
                                        <label>City </label>
                                        <select class="form-control" name="city"
                                                id="city" onchange="get_distributer()">
                                            <option value="">select</option>
                                            @foreach ($master->cities() as $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Distributor Name </label>
                                        <select required onchange="get_tso()" class="form-control distributor_id" name="distributor_id"
                                            id="distribuotr_id" required>
                                            <option value="">All</option>
                                            @foreach ($master->get_all_distributor_user_wise() as $row)
                                                <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>


                                </div>


                                <div class="col-md-4 tso_id">
                                    <div class="form-group">
                                        <label>TSO Name</label>
                                        <select class="form-control" id="tso_id" name="tso_id" required>
                                            <option value="">All</option>

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Product </label>
                                        <select required onchange="get_tso()" class="form-control" name="product_id"
                                            id="distribuotr_id" required>
                                            <option value="">All</option>
                                            @foreach ($master->get_all_product() as $row)
                                                <option value="{{ $row->id }}">{{ $row->product_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>


                                </div>



                                <div class="col-md-3 mt-2">
                                    <button onclick="get_ajax_data()" type="button"
                                        class="btn btn-primary mr-1">Generate
                                    </button>
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


            <div id="data">

            </div>
        </div>
    </div>
</div>




    </section>
    <!-- Basic Floating Label Form section end -->
@endsection
@section('script')
    <script>
        $(document).ready(function(){
            $('select').select2();
            $('.distributor_id').select2();
        });
        function type_change(){
            console.log($('#type').val());
            type = $('#type').val();
            if (type == 'Summary') {
                $('.city').hide();
                $('.tso_id').hide();
            }
            else if(type == 'Summary_new_details'){
                //$('.city').hide();
                $('.tso_id').hide();
            }
            else
            {
                $('.city').show();
                $('.tso_id').show();

            }
        }
    </script>

@endsection
