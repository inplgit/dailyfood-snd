@extends('layouts.master')
@section('title', 'TSO List')
@section('content')

    <div class="row" id="table-bordered">
        <div class="col-12">
            <div class="card">
                <form id="list_data" method="get" accept="{{ Request::url('') }}">

                    <div class="row">

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
                                <button type="button" onclick="get_ajax_data()" onclick="" name="submit" value="submit"
                                    class="btn btn-primary btn-xs">Generate</button>
                            </div>
                        </div>
                    </div>

                </form>
                <div class="card-header">
                    <h4 class="card-title">Order Booker Status Approval List</h4>
                    @can('TSO_Excel_Export')
                        <button type="button" id="exportBtn" onclick="exportBtn('TSOList')" class="btn btn-success">Export
                            Excel</button>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table id="dataTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" class="bulk-status-check-all" id="bulk-status-check-all">
                                </th>
                                <th>Sr No</th>
                                <th>TSO Code</th>
                                <th>TSO/Employee Name</th>
                                <th>Designation</th>
                                <th>City</th>

                                <th>CNIC</th>
                                <th>Phone NO</th>
                                <th>Store</th>
                                <th>User ID</th>
                                <th>Status</th>
                                <th>Date Of Join </th>
                                <th>Date Of Leaving</th>
                            </tr>
                        </thead>
                        <tbody id="data">
                        </tbody>
                    </table>
                    <button id="bulk-status-check-btn" disabled data-url="{{ route('tso.status_request_post') }}"
                        class="btn btn-primary btn-xs bulk-status-check-btn"  data-approve_reject="1" data-text="Are you sure you want to make changes"
                        type="button">Approve</button>
                    <button id="bulk-status-check-btn" disabled data-url="{{ route('tso.status_request_post') }}"
                        class="btn btn-primary btn-xs bulk-status-check-btn" data-approve_reject="0" data-text="Are you sure you want to make changes"
                        type="button">Reject</button>
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
