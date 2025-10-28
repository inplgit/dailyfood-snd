@php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
use App\Models\Product;
@endphp
@extends('layouts.master')
@section('title', 'Brand / Area Wise Daily Sales Sheet')
@section('content')
    <style>
    .print-butts{display:inline-flex;}
    </style>
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Brand / Area Wise Daily Sales Sheet</h4>
                           <button type="button" onclick="exportBtnWithFilters('Brand Wise Daily Sale')" class="btn btn-success">Export Excel</button>
                    </div>
                    <div class="card-body">
                        <form method="get" action="{{ route('brand_wise_daily_sale') }}" id="list_data" class="form">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>From</label>
                                        <input type="date"  name="from" id="export_from" class="form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>To</label>
                                        <input type="date"  name="to" id="export_to" class="form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">City</label>
                                        <select class="form-control" name="city" id="city"  onchange="getDistributorByCity()">
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
                                        <select required onchange="get_tso()" class="form-control" name="distributor_id"
                                            id="distribuotr_id" required>
                                            <option value="">All</option>
                                            @foreach ($master->get_all_distributor_user_wise() as $row)
                                                <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>TSO Name</label>
                                        <select onchange="get_route_by_tso()" class="form-control" id="tso_id" name="tso_id" required>
                                            <option value="">All</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2 col-12">
                                    <div class="form-group">
                                        <label class="control-label">Route</label>
                                        <select onchange="get_shop_by_route()" id="route_id" name="route_id" class="select2 form-control form-control-lg"></select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="cust2">
                                        <label class="control-label">Shop</label>
                                        <select class="form-control select2" id="shop_id" name="shop_id" required="">
                                            <option value="">Select a Shop </option>
                                        </select>
                                    </div>
                                </div>
                                <input type="hidden" id="pages" value="1">
                                <div class="mt-2">
                                    <div class="print-butts">
                                        <button onclick="get_ajax_data()" type="button"class="btn btn-primary mr-1">Generate</button>
                                        <button type="button" onclick="printTableLandscape('.printBody')" class="btn btn-primary text-right"> Print </button>
                                    </div>
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
                    <div id="data"></div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script>
            $( document ).ready(function() {
                $('select').select2();
            });

    </script>
@endsection

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
function printTableLandscape(tableId) {
    var printContents = document.querySelector(tableId).outerHTML;
    var win = window.open('', '', 'height=700,width=900');

    win.document.write('<html><head><title>Print</title>');
    win.document.write('<link rel="stylesheet" href="/css/app.css">');

    win.document.write(`
        <style>
        @media print {
            @page { 
                size: A4 landscape; 
                margin: 3px;
            }

            body {
                margin: 10mm;
                padding: 0;
                font-size: 12px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                table-layout: auto;
                word-wrap: break-word;
                font-size: 12px;
            }

            table, th, td {
                border: 1px solid black;
                padding: 6px;
                text-align: center;
            }

            .dates-info-head {
                text-align: center;
                margin: 5px 0;
                font-weight: bold;
            }

            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }
        </style>
    `);

    win.document.write('</head><body>');
    win.document.write(printContents);
    win.document.write('</body></html>');

    win.document.close();
    win.focus();
    win.print();
    win.close();
}




    function exportTableToExcel() {
        const fromDate = document.getElementById('export_from').value;
        const toDate   = document.getElementById('export_to').value;

        const table = document.getElementById('data1').querySelector('table');

        // Convert table to sheet
        const workbook = XLSX.utils.table_to_book(table);
        const worksheet = workbook.Sheets[workbook.SheetNames[0]];

        // Build the header row with From / To
        const extraHeader = [
            [`Report From: ${fromDate}   To: ${toDate}`]
        ];

        // Add it before the main table (shift everything down)
        XLSX.utils.sheet_add_aoa(worksheet, extraHeader, { origin: "A1" });

        // Save Excel
        XLSX.writeFile(workbook, 'brand_wise_daily_sale.xlsx');
    }
</script>


