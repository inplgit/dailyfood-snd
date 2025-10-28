
<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper;

$city =  Request::get('city') ?? [];
$distributor = Request::get('distributor_id') ?? null;
$tso_id = (Request::get('tso_id'))?? null;
$route_id = (Request::get('route_id'))?? null;
?>
@extends('layouts.master')
@section('title', "SND || Caregory")
@section('content')


<div class="card">
    <form id="list_data"  method="get" action="{{ Request::url('') }}">

        <div class="row">

            <div class="col-md-2 col-12">
                <div class="form-group">
                    <label class="control-label" >City</label>
                    <select  name="city[]" multiple id="city" class="select2 form-control form-control-lg">
                        <option value="">select</option>
                        @foreach ( $master->cities() as $row )
                         <option @if (in_array($row->id ,$city)) selected @endif value="{{ $row->id }}">{{ $row->name }}</option>
                        @endforeach

                    </select>
                </div>
            </div>

            <div class="col-md-2 col-12">
                <div class="form-group">
                    <label class="control-label" >Distributor</label>
                    <select onchange="get_tso()" name="distributor_id" id="distribuotr_id" class="select2 form-control form-control-lg">
                        <option value="">select</option>
                        @foreach ( $master->get_all_distributor_user_wise() as $row )
                         <option @if($distributor==$row->id) selected @endif value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                        @endforeach

                    </select>
                </div>
            </div>

            <div class="col-md-2 col-12">
                <div class="form-group">
                    <label class="control-label" >TSO</label>
                    <select onchange="get_route_by_tso()" name="tso_id" id="tso_id" class="select2 form-control form-control-lg">

                        <option></option>
                        <option value="1">1</option>
                    </select>
                </div>
            </div>

            <div class="col-md-2 col-12">
                <div class="form-group">
                    <label class="control-label" >Route</label>
                    <select id="route_id" name="route_id" class="select2 form-control form-control-lg">

                    </select>
                </div>
            </div>

            

            <div class="col-md-2 col-12">
                <div class="form-group">
                    <label class="control-label" >Status</label>
                    <select  name="status_request" id="status_request" class="select2 form-control form-control-lg">
                        <option value="2">Activate Request</option>
                        <option value="3">Deactivate Request</option>
                    </select>
                </div>
            </div>


            <div class="col-md-2 mb-3 text-left">
                <div class="generateshops">
                    <button type="button" onclick="get_ajax_data()"  onclick="" name="submit" value="submit" class="btn btn-primary btn-xs">Generate</button>
                </div>
              </div>
          </div>
        </div>

    </form>

</div>

   <span id="data"></span>
   {{-- @include('pages.Shop.Shops.ShopListAjax') --}}




@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            get_ajax_data();
        });







    </script>
@endsection
