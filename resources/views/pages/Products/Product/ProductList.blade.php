@extends('layouts.master')
@section('title', "Product")
@section('content')

<style>
    .no-view{
        display: none !important;
    }
</style>



    <form id="list_data"  method="get" action="{{ Request::url('') }}"></form>
    <div class="row" id="table-bordered">
        <div class="col-12">
            <div class="card" >
                <div class="card-header">
                    <h4 class="card-title">Product  List</h4>
                    <!-- ✅ Normal Page -->
                    <div class="no-print">
                    <button class="btn btn-primary prinn pritns" onclick="printSection()">🖨️ Print</button>
                    </div>
                </div>
                
                <div class="table-responsive" id="product_list">
                    <h4 class="card-title no-view">Product  List</h4>
                    <table class="table table-bordered" id="dataTable">
                        <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Category</th>
                            <th>Product Name</th>
                            <th>Brand</th>
                            <th>Product Type</th>
                            <th>UOM</th>
                            <th>Trade Price</th>
                            <th class="no-print">Actions</th>
                        </tr>
                        </thead>
                        <tbody id="data">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
    $(document).ready(function() {
        get_ajax_data();
    });
</script>
<script>
  function printSection() {
    // ✅ Print CSS dynamically add karna
    const printStyle = `
      @media print {
        @page{size:A4;margin:15mm 15mm 15mm 15mm !important;}
        .table-bordered > thead > tr > th,.table-bordered > tbody > tr > th,.table-bordered > tfoot > tr > th{vertical-align:bottom;border-bottom:2px solid #ddd;background:#dfe5ec;border:none !important;leading-trim:both;font-size:13px !important;font-style:normal;font-weight:600;line-height:normal;text-align:left;padding:10px 10px;color:#000;}
        .table-bordered > thead > tr > td,.table-bordered > tbody > tr > td,.table-bordered > tfoot > tr > td{padding:10px 10px;vertical-align:middle;font-size:14px;color:#000;font-style:normal;font-weight:600 !important;line-height:normal;border-left:none !important;border-right:none;border-bottom:1px solid #000 !important;text-align:left;}
        .no-print{display:none !important;}
        div#dataTable_length{display:none !important;}
        div#dataTable_filter{display:none !important;}
        div#dataTable_info{display:none !important;}
        div#dataTable_paginate{display:none !important;}
      }
    `;

    // ✅ Select element to print
    const printContent = document.getElementById('product_list').innerHTML;
    // ✅ Open new window for print
    const printWindow = window.open('', '', 'width=900,height=700');
    // ✅ Bootstrap 5 CSS link
    const bootstrapCSS = `<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">`;
    // ✅ Write content to print window
    printWindow.document.write(`
      <html>
      <head>
        <title>Print Preview</title>
        ${bootstrapCSS}
        <style>${printStyle}</style>
      </head>
      <body>
        ${printContent}
      </body>
      </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    // printWindow.close(); // optional
  }
</script>
<!-- </head>
<body> -->
@endsection
