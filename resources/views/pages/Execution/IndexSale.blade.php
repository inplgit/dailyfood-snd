
<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>
@extends('layouts.master')
@section('title', 'SND || Departments')
@section('content')


<style>
    #ajax-loader i {
        font-size: 48px;
    }
</style>

<div class="card">
    <form id="list_data" method="get" action="{{ route('sale-order.execution.index') }}">

        <div class="form-row">

            <div class="col-md-2 mb-2">
                <label class="control-label" for="start-date">From</label>
                <input type="date" value="" class="form-control" name="from" placeholder="Start date"
                    required>
            </div>
            <div class="col-md-2 mb-2">
                <label class="control-label" for="end-date">To</label>
                <input type="date" value="" class="form-control" name="to" placeholder="End date"
                    required>
            </div>


            <div class="col-md-2 mb-2">
                <div class="form-group">
                    <label class="control-label">City</label>
                    <select class="form-control" name="city" id="city"  onchange="getDistributorByCity()">
                            id="" required>
                        <option value="">select</option>
                        @foreach ($master->cities() as $row)
                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-2 mb-2">
                <div class="form-group">
                    <label class="control-label">Distributor Name</label>
                    <select onchange="get_tso()" class="form-control" name="distributor_id" id="distribuotr_id">
                        <option value="">select</option>
                        @foreach ($master->get_all_distributor_user_wise() as $row)
                            <option value="{{ $row->id }}">{{ $row->distributor_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-2 mb-2">
                <div class="form-group">
                    <label class="control-label">TSO Name</label>
                    <select class="form-control" id="tso_id" name="tso_id">
                        <option value="">select</option>
                    </select>
                </div>
            </div>

            <input type="hidden" value="0" name="execution" />
            <div class="col-md-2 mb-3">
                <div class="gers">
                    <button type="button" onclick="get_ajax_data()" class="btn btn-primary btn-xs">Generate</button>
                </div>
            </div>
        </div>
 <!-- Inside the <form id="list_data"> -->
                            <div class="col-md-2 mb-2">
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

</div>

    <div class="row" id="table-bordered">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Sale Order Executioin List</h4>


			<div>

                    @can('Sale_Order_Execute')
                        <button class="m-1 btn btn-primary" disabled id="bulk-execution-check-btn" data-url="{{ route('sale-order.execution.bulk') }}" type="button">Execute </button>
                    @endcan


			<button class="m-1 btn btn-danger" disabled id="bulk-execution-check-btn1"
                    data-url="{{ route('sale-order.delete.bulk') }}" type="button">Bulk Delete</button>

			</div>
                </div>

                <div class="table-responsive">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" class="bulk-execution-check-all" id="bulk-execution-check-all"> </th>
                                <th>Sr No</th>
                                <th>Inoivce No.</th>
                                <th>Inoivce Date.</th>
                                <!-- <th>Delivery Date.</th> -->
                                <th>Distributor</th>
                                <th>TSO</th>
                                <th>City</th>
                                <th>Shop</th>
                                <th>Execution</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
 <!-- 🔄 Loader -->
<div id="ajax-loader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:9999; text-align:center;">
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">
        <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
        <p>Loading...</p>
    </div>
</div>

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
    let checkedRecords = [];
    const bulkExecuteBtn = $('#bulk-execution-check-btn');
    const bulkDeleteBtn = $('#bulk-execution-check-btn1');
    const loader = $('#ajax-loader');
    const dataContainer = $('#data');
    const searchInput = $('input[name="search"]');

    // Initial data load
    loadData();

    // Handle individual checkboxes
    $(document).on('change', '.bulk-execution-check', function() {
        const id = $(this).val();
        if ($(this).is(':checked')) {
            if (!checkedRecords.includes(id)) {
                checkedRecords.push(id);
            }
        } else {
            checkedRecords = checkedRecords.filter(item => item !== id);
        }
        updateButtonStates();
    });

    // Select/Deselect all
    $(document).on('change', '.bulk-execution-check-all', function() {
        checkedRecords = [];
        $('.bulk-execution-check').prop('checked', this.checked);
        if (this.checked) {
            $('.bulk-execution-check').each(function() {
                checkedRecords.push($(this).val());
            });
        }
        updateButtonStates();
    });

    // Bulk Execute
    bulkExecuteBtn.on('click', function() {
        if (!validateSelection()) return;
        performBulkAction($(this).data('url'), 'execute');
    });

    // Bulk Delete
    bulkDeleteBtn.on('click', function() {
        if (!validateSelection()) return;
        if (!confirm('Are you sure you want to delete selected records?')) return;
        performBulkAction($(this).data('url'), 'delete');
    });

    // Debounced Search
    searchInput.on('input', debounce(loadData, 500));

    // ========== Helper Functions ========== //
    function loadData() {
        showLoader();
        $.ajax({
            url: "{{ route('sale-order.execution.index') }}",
            type: "GET",
            data: $("#list_data").serialize(),
            success: function(response) {
                dataContainer.html(response);
                resetSelection();
            },
            complete: hideLoader
        });
    }

    function performBulkAction(url, actionType) {
        showLoader();
        $.ajax({
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            url: url,
            data: {
                checked_records: checkedRecords,
                action: actionType
            },
            success: function(response) {
                let message = response.msg || "Operation completed successfully.";
                if (response.stock) {
                    message += "\n\nStock Status:\n" + response.stock;
                }
                alert(message);
                loadData();
            },
            error: function(xhr) {
                let errorMsg = "Operation failed.";
                if (xhr.responseJSON?.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
                console.error(xhr.responseText);
            },
            complete: hideLoader
        });
    }

    function validateSelection() {
        if (checkedRecords.length === 0) {
            alert("Please select at least one record.");
            return false;
        }
        return true;
    }

    function updateButtonStates() {
        const hasSelections = checkedRecords.length > 0;
        bulkExecuteBtn.prop('disabled', !hasSelections);
        bulkDeleteBtn.prop('disabled', !hasSelections);
    }

    function resetSelection() {
        checkedRecords = [];
        $('.bulk-execution-check-all, .bulk-execution-check').prop('checked', false);
        updateButtonStates();
    }

    function showLoader() {
        loader.fadeIn();
    }

    function hideLoader() {
        loader.fadeOut();
    }

    function debounce(func, delay) {
        let timer;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, args), delay);
        };
    }
});
</script>
@endsection
