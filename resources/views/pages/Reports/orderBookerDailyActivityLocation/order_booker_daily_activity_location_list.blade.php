@php

use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
use App\Models\Product;
@endphp
@extends('layouts.master')
@section('title', 'TSO Activity')
@section('content')
{{-- @dd('REPORT UNDER MAINTENANCE') --}}
<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Order Booker Daily Activity Timestamp Report</h4>
                    <button type="button" id="exportBtn" onclick="exportBtnWithFilters('Order Booker Daily Activity Timestamp Report')" class="btn btn-success">Export Excel</button>
                </div>
                <div class="card-body">
                    <form method="get" action="{{ route('order_booker_daily_activity_location_report') }}" id="list_data" class="form">
                        @csrf
                        <div class="row">

                           <div class="col-md-4">
                                    <div class="form-group">
                                        <label>From</label>
                                        <input type="date"  name="from" id="date" class="form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>To</label>
                                        <input type="date"  name="to" id="date" class="form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Distributor Name </label>
                                    <select onchange="get_tso()" class="form-control" name="distributor_id"
                                        id="distribuotr_id" required>
                                        <option value="">All</option>
                                        @foreach ($master->get_all_distributor_user_wise() as $row)
                                        <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>TSO Name</label>
                                    <select onchange="get_route_by_tso()" class="form-control" id="tso_id" name="tso_id">
                                        <option value="">All</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Route</label>
                                    <select onchange="get_shop_by_route()" id="route_id" name="route_id" class="select2 form-control form-control-lg"></select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="cust2">
                                    <label class="control-label">Shop</label>
                                    <select class="form-control select2" id="shop_id" name="shop_id">
                                        <option value="">Select a Shop </option>
                                    </select>
                                </div>
                            </div>

                            <input type="hidden" id="pages" value="1">


                            <div class="col-md-3 mt-2">
                                <div class="print-butts">
                                    <button type="button"class="btn btn-primary mr-1 submitBtn">Generate</button>
                                    <button type="button" onclick="printTableNew('.printBody')" class="btn btn-primary text-right"> Print </button>
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


                <div id="data">

                </div>
            </div>
        </div>
    </div>




</section>
<!-- Basic Floating Label Form section end -->
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('select').select2();
    });
    $('.submitBtn').click(function(){
        let form = $("#list_data")[0];
        if (!form.checkValidity()) {
            form.reportValidity();
            alert("Please select Distributor Name");
            return false;
        }
        get_ajax_data();
    })

    // function get_ajax_data() {
    //     let form = $("#list_data")[0];

    //     // ✅ Run HTML5 built-in required validation
    //     if (!form.checkValidity()) {
    //         form.reportValidity(); // shows browser's default validation popup
    //         alert("Please select Distributor Name");
    //         return false;
    //     }
    //     console.log("All required fields are filled.");
    // }

//    function get_ajax_data() {
//         let form = document.getElementById("list_data");

//         // Required validation check
//         if (!form.checkValidity()) {
//             form.reportValidity();
//             return false;
//         }

//         // FormData banayenge
//         let formData = $("#list_data").serialize();

//         $.ajax({
//             url: "{{ route('order_booker_daily_activity_location_report') }}", // wahi route jahan data chahiye
//             type: "GET", // aap GET use kar rahe ho
//             data: formData,
//             beforeSend: function() {
//                 $("#data").html("<p>Loading...</p>");
//             },
//             success: function(response) {
//                 $("#data").html(response); // response ko div me dikhana
//             },
//             error: function(xhr) {
//                 console.error(xhr.responseText);
              
//             }
//         });
//     }
</script>
@endsection
