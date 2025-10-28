@extends('layouts.master')
@section('title', 'SND || Payment Recovery Execution')
@section('content')


<div class="card">
    
    <form id="list_data" method="get" action="{{ route('payment-recovery.execution.index') }}">
        <div class="form-row">
            <div class="col-md-3 mb-3">
                <label for="start-date">From</label>
                <input type="date" value="{{ date('Y-m-01') }}" class="form-control" name="from" placeholder="Start date"
                    required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="end-date">To</label>
                <input type="hidden" value="0" name="execution" />
                <input type="date" value="{{ date('Y-m-t') }}" class="form-control" name="to" placeholder="End date"
                    required>
            </div>

            <div class="col-md-3 mb-3">
                <button type="button" onclick="get_ajax_data()" class="btn btn-primary btn-xs">Generate</button>
            </div>
        </div>
    </form>
</div>
    <div class="row" id="table-bordered">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Payment Recovery List</h4>
                    @can('Payment_Recovery_Execute')
                        <button class="m-1 btn btn-primary" disabled id="bulk-execution-check-btn"
                            data-url="{{ route('payment-recovery.execution.bulk') }}" type="button">Execute All</button>
                    @endcan
                </div>
                <div class="table-responsive">
                 
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" class="bulk-execution-check-all" id="bulk-execution-check-all">
                                </th>
                                <th>Sr No</th>
                                <th>Date</th>
                                <th>Distributor</th>
                                <th>TSO</th>
                                <th>Shop</th>
                                <th>Delivery Man</th>
                                <th>Route</th>
                                <th>Execution</th>
                                <th>Amount</th>
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
                        'checked_records': checked
                    },
                    success: function(response, textStatus, xhr) {
                        if (response.status) {
                            alert(response.msg);
                            location.reload();
                        }
                    }
                });
            });
        });
    </script>
@endsection
