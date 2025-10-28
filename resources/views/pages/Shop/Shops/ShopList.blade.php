
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
                    <select  name="city[]" multiple id="city" class="select2 form-control form-control-lg" onchange="getDistributorByCity()">
                        {{-- <option value="">select</option> --}}
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
                        {{-- master->get_all_distributor_user_wise() --}}
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
            <div class="col-md-2">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" id="date" class="form-control"
                        value="" required="true">
                </div>
            </div>
        {{-- </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" name="from_date" id="from_date" class="form-control"
                        value="{{ date('Y-m-d') }}" required="true">
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" name="to_date" id="to_date" class="form-control"
                        value="{{ date('Y-m-d') }}" required="true">
                </div>
            </div> --}}

            <div class="col-md-1 mb-3 text-left">
                <div class="generateshops">
                    <button type="button" onclick="get_ajax_dataTable()" name="submit" value="submit" class="btn btn-primary btn-xs">Generate</button>
                </div>
              </div>
            <div class="col-md-1 mb-3 text-left">
                <div class="generateshops">
                    <button type="button" onclick="reset_form('list_data')"  class="btn btn-danger btn-xs">Clear</button>
                </div>
            </div>
          </div>
        </div>

    </form>

</div>

   <span id="data">
    <div class="row" id="table-bordered">

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Shop List<span class="badge badge-success"></span></h4>
                    <button type="button" id="exportBtn" onclick="exportExcelBtn('ShopList' , '{{ route('export.shops') }}')" class="btn btn-success">Export
                        Excel</button>
                </div>
                <div class="table-responsive">
                    <table id="dataTable" class="table table-bordered yajra-table">
                        <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Shop Code</th>
                            <th>Shop Name</th>
                            <th>City</th>
                            <th>Distributor</th>
                            <th>TSO</th>
                            <th>Route</th>
                            <th>Sub Routes</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody id="data">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
       </div>
      </div>

   </span>
   {{-- @include('pages.Shop.Shops.ShopListAjax') --}}




@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            get_tso();
            // get_ajax_data();
            // getDistributorByCity();
            setTimeout(setTso, 2000);


            function setTso() {
                var tso = '{{ $tso_id }}';
                if (tso != null)
                {
                    $('#tso_id').val(tso).trigger('change');
                    get_route_by_tso();
                    setTimeout(setRoute, 3000);
                }
            }


            function setRoute() {
                var route_id = '{{ $route_id }}';

                if (route_id != null)
                {
                    $('#route_id').val(route_id).trigger('change');

                }
            }



        });




        $(document).ready(function() {

            var columns = [
                    // { data: 'id', name: 'id' },
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }, // Auto-increment column
                    { data: 'shop_code', name: 'shop_code' },
                    { data: 'company_name', name: 'company_name' },
                    { data: 'city', name: 'city' },
                    { data: 'distributor', name: 'distributor' },
                    { data: 'tso', name: 'tso' },
                    { data: 'route', name: 'route' },
                    { data: 'sub_route', name: 'sub_route' },
                    { data: 'status', name: 'status' },
                    { data: 'remarks', name: 'remarks' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ];

            // Define custom filter inputs that you want to send with the AJAX request
            var filterInputs = [
                {name: 'distributor_id', selector: '#distribuotr_id'},
                {name: 'tso_id', selector: '#tso_id'},
                {name: 'city', selector: '#city'},
                {name: 'route_id', selector: '#route_id'},
                {name: 'date', selector: '#date'}
            ];

            // Initialize DataTable with custom search params
            data_table().initialize('{{ Request::url('') }}', columns, filterInputs);


        });




    </script>
@endsection
