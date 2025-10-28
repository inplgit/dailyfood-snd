@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
@endphp

@extends('layouts.master')
@section('title', 'Brand Distribution Report')
@section('content')
    <style>
        .print-butts {
            display: inline-flex;
            gap: 5px;
        }

        .print-butts2 {

            margin-top: -51px;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Brand Distribution Report</h4>
                        <button type="button" onclick="exportBtnWithFilters('Brand Distribution Report')"
                            class="btn btn-success">Export Excel</button>

                    </div>
                    <div class="card-body">
                        <form method="get" action="{{ route('brand_distributer_report') }}" id="list_data" class="form">
                            @csrf
                            <div class="row align-items-end">

                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">From</label>
                                                <input type="date" name="from" class="form-control select2"
                                                    value="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">To</label>
                                                <input type="date" name="to" class="form-control select2"
                                                    value="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">City</label>
                                                <select required class="form-control select2" name="city">
                                                    <option value="">select</option>
                                                    @foreach ($master->cities() as $row)
                                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Distributor Name</label>
                                                <select required onchange="get_tso()" class="form-control select2"
                                                    name="distributor_id" id="distribuotr_id">
                                                    <option value="">select</option>
                                                    @foreach ($master->get_all_distributor_user_wise() as $row)
                                                        <option value="{{ $row->id }}">{{ $row->distributor_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        {{-- <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">TSO Name</label>
                                            <select class="form-control select2" id="tso_id" name="tso_id" required>
                                                <option value="">select</option>
                                            </select>
                                        </div>
                                    </div> --}}

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>TSO Name</label>
                                                <select onchange="get_route_by_tso()" class="form-control select2"
                                                    id="tso_id" name="tso_id" required>
                                                    <option value="">All</option>
                                                    {{-- @foreach ($master->get_all_tso() as $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach --}}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label class="control-label">Route</label>
                                                <select onchange="get_shop_by_route()" id="route_id" name="route_id"
                                                    class="form-control select2 form-control-lg"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="cust2">
                                                <label class="control-label">Shop</label>
                                                <select class="form-control select2" id="shop_id" name="shop_id"
                                                    required="">
                                                    <option value="">Select a Shop </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Designation Name</label>
                                                <select required class="form-control select2" name="designation">
                                                    <option value="">select</option>
                                                    @foreach ($master->get_all_designation() as $row)
                                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Products</label>
                                                <select class="form-control select2" name="product_id">
                                                    <option value="">select</option>
                                                    @foreach ($master->get_all_product() as $row)
                                                        <option value="{{ $row->id }}">{{ $row->product_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="print-butts print-butts2">
                                        <button onclick="get_ajax_data()" type="button"
                                            class="btn btn-primary mr-1">Generate</button>
                                        <button type="button" onclick="printTableNew('.printBody')"
                                            class="btn btn-primary">Print</button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Report Data --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div id="data"></div>
                </div>
            </div>
        </div>
    </section>

@endsection


<script>
    function exportTableToExcel() {
        const table = document.getElementById('data1').querySelector('table');
        const workbook = XLSX.utils.table_to_book(table);
        XLSX.writeFile(workbook, 'brand_distribution_report.xlsx');
    }
</script>


@section('script')

    <script>
        $(document).ready(function() {
            $('select').select2();
        });
    </script>

@endsection
