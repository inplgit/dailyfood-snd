@extends('layouts.master')
@section('title', 'Designations')
@section('content')


    <form id="list_data" method="get" action="{{ route('designation.index') }}"></form>
    <div class="row" id="table-bordered">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Designation List</h4>
                    @can('Designation_Create')
                        <a href="{{ route('designation.create') }}" class="btn btn-success mr-1">Create</a>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Actions</th>
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
