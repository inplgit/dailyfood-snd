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
                        <h4 class="card-title">Shop Visit / Merchandising</h4>
                        <button type="button" id="exportBtn" onclick="exportBtn('Order Summary')" class="btn btn-success">Export Excel</button>
                    </div>
                    <div class="card-body">
                        <form method="get" action="{{ route('shop.shopVisitList') }}" id="list_data" class="form">
                            @csrf
                            <div class="row">

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>From</label>
                                        <input type="date"  name="from" id="date" class="form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>To</label>
                                        <input type="date"  name="to" id="date" class="form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>


                               <div class="col-md-3">

                <label>Distributor Name</label>
               <select  onchange="get_tso_all()" class="form-control" id="distribuotr_id" name="distribuotr_id">
                <option value="">select</option>
                @foreach ( $master->get_all_distributor_user_wise() as $row )
                   <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                @endforeach

               </select>
            </div>

            <div onchange="get_ajax_data()" class="col-md-3">
                <label>TSO Name</label>
               <select class="form-control" name="tso_id" id="tso_id">
                <option value="">select</option>
               </select>
            </div>
        </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>TSO Name</label>
                                        <select class="form-control" id="type" name="type" required>
                                            <option value="0">Visit</option>
                                            <option value="1">Merchandising</option>
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
