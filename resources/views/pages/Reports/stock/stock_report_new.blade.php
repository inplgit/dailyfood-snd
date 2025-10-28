<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>
@extends('layouts.master')
@section('title', "SND || Caregory")
@section('content')
  <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Stock Report</h4>
                        <button type="button" id="" onclick="exportView('table_data','')" class="btn btn-success">Export Excel</button>
                           
                    </div>
                    <div class="card-body">
                        <form method="get" action="{{ route('stock_report_new') }}"  id="list_data" class="form">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>From * </label>
                                        <input type="date" class="form-control" name="from" id="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label> Till Date * </label>
                                        <input type="date" class="form-control" value="{{date('Y-m-d')}}" name="to" id="">
                                    </div>
                                </div>
                                <div class="col-md-3">
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
                                <div class="col-md-3 distributor_area">
                                    <div class="form-group">
                                        <label>Distributor Name* </label>
                                        <select required onchange="get_tso()" class="form-control" name="distributor_id" id="distribuotr_id">
                                            <option value="">select</option>
                                            @foreach ( $master->get_all_distributor_user_wise() as $row )
                                                <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
				<div class="col-md-3">
                                    <div class="form-group">
                                        <label>Product </label>
                                        <select required class="form-control" name="product_id"
                                            id="product_id" required>
                                            <option value="">All</option>
                                            @foreach ($master->get_all_product() as $row)
                                                <option value="{{ $row->id }}">{{ $row->product_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>


                                </div>
				                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Select Type </label>
                                        <select class="form-control" name="type" id="type">
                                            	<option value="">select</option>
                                           	<option value="carton">Carton</option>
						                    <option value="qty">Qty</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Summary/Detail </label>
                                        <select class="form-control" name="detail" id="detail">
                                             <option value="detail">Detail</option>	
                                           	<option value="summary">Summary</option>
						                   
                                        </select>
                                    </div>
                                </div>
                                

                                {{-- <div class="col-md-4">
                                    <div class="form-group">
                                        <label>TSO Name</label>
                                        <select class="form-control" id="tso_id" name="tso_id">
                                            <option value="">select</option>
                                            {{-- @foreach ($master->get_all_tso() as $row )
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div> --}}
                                {{-- <div class="col-md-4">
                                    <div class="form-group">
                                        <label for=""><h5>Summary</h5></label>
                                        <input type="radio" id="yes" name="summary" value="1">
                                        <label for="yes">yes</label>
                                        <input type="radio" id="no" checked name="summary" value="0">
                                        <label for="no">No</label>
                                    </div>
                                </div> --}}
                                <div class="col-md-1" style="margin-top: 23px;">

                                    <button onclick="get_ajax_data()" type="button" class="btn btn-primary mr-1">Generate</button>
                                </div>
                                <div class="col-1" style="margin-top: 23px;">
                                <button type="button" onclick="printTable('data')" class="btn btn-primary mr-1 text-right right">Print</button>
                            </div>
                            


                            </div>
                        </form>
                        <form method="post" action="{{ route('stock_report') }}">
                            @csrf
                        <div class="table-responsive" >
                            <div id="data">

                            </div>
                        </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Floating Label Form section end -->

@endsection
<script src="{{ URL::asset('assets/js/xlsx.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
 <script>
    $(document).ready(function(){
        $('#distribuotr_id').select2();
        $('#product_id').select2();

        $(document).on('change', '#detail', function() {
        var val = $(this).val();
        if (val === 'summary') {
            $('.distributor_area').css({'display': 'none'});
            $('.city_area').css({'display': 'none'});
        } else {
            $('.distributor_area').css({'display': 'block'});
            $('.city_area').css({'display': 'block'});
        }
    });
    })

   
 </script> 
<script>

     

    function printTable(tableId) {
        var printContents = document.getElementById(tableId).outerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();

        document.body.innerHTML = originalContents;
    }
    
    function exportView(tableId, filename) {
    
    filename='download.xlsx';
    
    const table = document.getElementById(tableId);

    // Create a worksheet with default styles
    const ws = XLSX.utils.table_to_sheet(table, { sheet: "Sheet 1" });

    // Define cell styles
    const styles = {
      header: {
        font: { bold: true, color: { rgb: "FF0000" } },
        fill: { fgColor: { rgb: "FFFF00" } },
        alignment: { horizontal: "center" },
        border: {
          top: { style: "thin" },
          bottom: { style: "thin" },
          left: { style: "thin" },
          right: { style: "thin" },
        },
      },
      cell: {
        border: {
          top: { style: "thin" },
          bottom: { style: "thin" },
          left: { style: "thin" },
          right: { style: "thin" },
        },
      },
    };

    // Apply styles to the worksheet
    XLSX.utils.format_cell(ws.A1, styles.header);
    XLSX.utils.format_cell(ws.B1, styles.header);

    for (let i = 2; i <= table.rows.length; i++) {
      XLSX.utils.format_cell(ws["A" + i], styles.cell);
      XLSX.utils.format_cell(ws["B" + i], styles.cell);
    }

    // Create a workbook and add the worksheet
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Sheet 1");

    // Save the workbook as a file
    XLSX.writeFile(wb, filename);
  }


// function exportView(param1,param2,$param3) {
//     var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
//     var textRange; var j = 0;
//     tab = document.getElementById(param1);//.getElementsByTagName('table'); // id of table
//     if (tab==null) {
//         return false;
//     }
//     if (tab.rows.length == 0) {
//         return false;
//     }

//     var a= tab
//     for (j = 0 ; j < a.rows.length ; j++)
//     {

//         if(a.rows[j].children[a.rows[j].children.length - 1 ].id == 'hide-table-row')
//         {
//             a.rows[j].removeChild(a.rows[j].children[[a.rows[j].children.length - 1 ]])
//         }

//         tab_text = tab_text + a.rows[j].innerHTML + "</tr>";
//     }

//     tab_text = tab_text + "</table>";
//     tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
//     tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
//     tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params
//     document.getElementsByClassName('show_data').removeClass;

//     var ua = window.navigator.userAgent;
//     var msie = ua.indexOf("MSIE ");

//     if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
//     {
//         txtArea1.document.open("txt/html", "replace");
//         txtArea1.document.write(tab_text);
//         txtArea1.document.close();
//         txtArea1.focus();
//         sa = txtArea1.document.execCommand("SaveAs", true, "download.xlsx");
//     }
//     else                 //other browser not tested on IE 11
//     //sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
//         try {
//             var blob = new Blob([tab_text], { type: "application/vnd.ms-excel" });
//             window.URL = window.URL || window.webkitURL;
//             link = window.URL.createObjectURL(blob);
//             a = document.createElement("a");
//             if (document.getElementById("caption")!=null) {
//                 a.download=document.getElementById("caption").innerText;
//             }
//             else
//             {
//                 a.download = 'download';
//             }

//             a.href = link;

//             document.body.appendChild(a);

//             a.click();

//             document.body.removeChild(a);
//         } catch (e) {
//         }


//     return false;
//     //return (sa);
// }

jQuery.fn.tableToCSV = function() {
    var clean_text = function(text){
        text = text.replace(/"/g, '""');
        return '"'+text+'"';
    };

    $(this).each(function(){
        var table = $(this);
        var caption = $(this).find('caption').text();
        var title = [];
        var rows = [];

        $(this).find('tr').each(function(){
            var data = [];
            $(this).find('th').each(function(){
                var text = clean_text($(this).text());
                title.push(text);
            });
            $(this).find('td').each(function(){
                var text = clean_text($(this).text());
                data.push(text);
            });
            data = data.join(",");
            rows.push(data);
        });
        title = title.join(",");
        rows = rows.join("\n");

        var csv = title + rows;
        var uri = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
        var download_link = document.createElement('a');
        download_link.href = uri;
        var ts = new Date().getTime();
        if(caption==""){
            download_link.download = ts+".csv";
        } else {
            download_link.download = caption+"-"+ts+".csv";
        }
        document.body.appendChild(download_link);
        download_link.click();
        document.body.removeChild(download_link);
    });
};

</script>

<script>

function detail(val){
        
        if(val == 'summary'){
            $('.distributor_area').css({'display': 'none'});
        } else {
            $('.distributor_area').css({'display': 'block'});
        }
    }

</script>