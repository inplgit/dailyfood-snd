@extends('layouts.master')
@section('title', 'Sales Return')
@section('content')

<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>




<form id="list_data" method="get" action="{{isset($excution) ? route('sales_return.sales_return_list_shop_wise', ['excution' => 'execution']) : route('sales_return.index') }}" class="p-3 bg-light border rounded">
   

 <div class="row g-3 align-items-end">
        <!-- From Date -->
        <div class="col-md-2">
            <label class="form-label fw-bold" for="start-date">From</label>
            <input type="date" value="{{ date('Y-m-d') }}" class="form-control" name="from" placeholder="Start date" required>
        </div>

        <!-- To Date -->
        <div class="col-md-2">
            <label class="form-label fw-bold" for="end-date">To</label>
            <input type="date" value="{{ date('Y-m-d') }}" class="form-control" name="to" placeholder="End date" required>
        </div>

        <!-- City -->
        <div class="col-md-2">
            <label class="form-label fw-bold">City</label>
            <select class="form-control" name="city" id="city"  onchange="getDistributorByCity()">
                <option value="" selected>Select</option>
                @foreach ($master->cities() as $row)
                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Distributor Name -->
        <div class="col-md-2">
            <label class="form-label fw-bold">Distributor Name</label>
            <select class="form-control" name="distributor_id" id="distribuotr_id" onchange="get_tso()">
                <option value="" selected>Select</option>
                @foreach ($master->get_all_distributor_user_wise() as $row)
                    <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- TSO Name -->
        <div class="col-md-2">
            <label class="form-label fw-bold">TSO Name</label>
            <select class="form-control" id="tso_id" name="tso_id">
                <option value="" selected>Select</option>
            </select>
        </div>

        <!-- Generate Button -->
        <div class="col-md-2 text-end">
            <button type="button" onclick="get_ajax_data()" class="btn btn-primary w-100">Generate</button>
        </div>
    </div>


     <!-- Inside the <form id="list_data"> -->
                            <div class="col-md-2 mb-2" style="margin-top: 27px;">
                                <label class="control-label">Search</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    name="search" 
                                    placeholder="Invoice, Distributor, TSO, etc."
                                    value="{{ request('search') }}"
                                    style="
                                width: 160%;
                            "
                                >
                            </div>
</form>






<form id="list_data" method="get" action="{{isset($excution) ? route('sales_return.sales_return_list', ['excution' => 'execution']) : route('sales_return.index') }}">
        


<div class="row" id="table-bordered">
            <form method="post" action="{{ route('sales_return.sales_return_execution_submit') }}">
                @csrf
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Sales Return List</h4>
                            @if (isset($excution))
                            @can('Sale_Return_Execute')
                                    <button type="button" disabled id="bulk-execution-check-btn" data-url="{{ route('sales_return.sales_return_execution_submit') }}"  class="btn btn-primary mr-1 text-right right">Execution</button>
                            @endcan
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        @if (isset($excution))
                                            <th>
                                                {{-- <input type="checkbox" class="checkbox" class="check" /> --}}
                                                <input type="checkbox" class="bulk-execution-check-all" id="bulk-execution-check-all">
                                            </th>
                                        @endif
                                        <th>Sr No</th>
                                        <th>Sales Return No</th>
                                      
                                        <th>Distributor</th>
                                        <th>TSO</th>
                                        <th>Shop</th>
                                        <th>Execution</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                                <tbody id="data">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </form>
    <!-- Basic Floating Label Form section end -->


@endsection
@section('script')
    <script>
        // var $j = jQuery.noConflict();
        // $j(document).ready(function() {
        //     get_ajax_data();
        // });


        $(document).ready(function() {
            get_ajax_data();
            var checked = [];
            $(document).on('change', '.bulk-execution-check', function() {
                $('#bulk-execution-check-btn').prop('disabled', false);
                if (this.checked) {
                    // alert('checked');
                    checked.push($(this).val());
                    console.log(checked);
                } else {
                    // alert('no checked');
                    checked.pop();
                    console.log(checked);
                }

            });
            $(document).on('change', '.bulk-execution-check-all', function() {
                $('#bulk-execution-check-btn').prop('disabled', false);
                if (this.checked) {
                    $('.bulk-execution-check').prop('checked', true);
                    $(".bulk-execution-check:checked").each(function() {
                        checked.push($(this).val());
                    });
                } else {
                    checked = [];
                    $('.bulk-execution-check').prop('checked', false);
                }

            });
            $('#bulk-execution-check-btn').on('click', function() {


                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: $(this).data('url'),
                    data: {
                        'id': checked
                    },
                  success: function(response, textStatus, xhr) {
    if (response.catchError) {
        $(".print-error-msg").find("ul").html('');
        $(".print-error-msg").css('display', 'block');
        $(".print-error-msg").find("ul").append('<li>' + response.catchError + '</li>');
        window.scrollTo(0, 0);
        return;
    }
    if ($.isEmptyObject(response.error)) {
        $(".alert-success").find("ul").html('<li>' + response.success + '</li>');
        $("#subm").trigger("reset");
        get_ajax_data();
        $('#unique_code').val(response.code);
    } else {
        printErrorMsg(response.error);
    }
}

                });
            });
        });

  function get_ajax_data() {
        $.ajax({
            url: $("#list_data").attr('action'),
            type: "GET",
            data: $("#list_data").serialize(),
            success: function(response) {
                $("#data").html(response);
            },
            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });
    }

    let searchTimer;
$('input[name="search"]').on('input', function() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function() {
        get_ajax_data();
    }, 500); // 500ms delay
});

    </script>
@endsection
