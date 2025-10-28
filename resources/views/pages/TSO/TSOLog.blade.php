@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
@endphp
@extends('layouts.master')
@section('title', 'Order Booker List')
@section('content')



    </form>
    <div class="row" id="table-bordered">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Order Booker Log</h4>
                    @can('TSO_Excel_Export')
                        <button type="button" id="exportBtn" onclick="exportBtn('TSOList')" class="btn btn-success">Export
                            Excel</button>
                    @endcan
                </div>
                <div class="card-body">
                    <form method="get" action="{{ Request::url('') }}" id="list_data" class="form">
                        @csrf
                        <div class="row">


                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>TSO Name</label>
                                    <select class="form-control select2" id="tso_id" name="tso_id" required>
                                        <option value="">select</option>
                                        @foreach ($master->get_all_tso() as $row)
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 mt-2">
                                <button onclick="get_ajax_data()" type="button" class="btn btn-primary mr-1">Generate
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table id="dataTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Method</th>
                                <th>Date</th>
                                <th>Title</th>
                                <th>OB Name</th>
                                <th>Distributors</th>

                                <th>User Name</th>
                            </tr>
                        </thead>
                        <tbody id="data">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Basic Floating Label Form section end -->



@endsection
@section('script')
    <script>
        $(document).ready(function() {
            get_ajax_data();
        });
    </script>
@endsection
