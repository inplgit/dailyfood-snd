<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
use App\Models\Shop;

?>
<div class="row" id="table-bordered">

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Shop List<span class="badge badge-success"></span></h4>
            </div>
            @if (isset($shop))
                    <div class="table-responsive">
                        <table id="dataTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" class="bulk-status-check-all" id="bulk-status-check-all">
                                    </th>
                                    <th>Sr No</th>
                                    <th>Shop Code</th>
                                    <th>Shop Name</th>
                                    <th>City</th>
                                    <th>Distributor</th>
                                    <th>TSO</th>
                                    <th>Route</th>
                                    <th>Sub Routes</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="data">
                                <input type="hidden" name="status_request_data" value="{{$status_request}}" id="status_request_data">

                                @foreach ($shop as $key => $row)
                                    <tr>
                                        <td><input type="checkbox" name=""
                                                value="{{ $row->id }}" class="bulk-status-check" id="">
                                        </td>
                                        <td>{{ ++$key }}</td>
                                        <td>{{ $row->shop_code }}</td>
                                        <td>{{ $row->company_name }}</td>
                                        <td> {{ $row->city ? $row->cities->name ?? '' : '' }}</td>
                                        <td>{{ $row->distributor_id ? $row->distributor->distributor_name : '' }}</td>
                                        <td>{{ $row->tso->name ?? '' }}</td>
                                        <td>{{ $row->route->route_name ?? $row->route_id }}</td>
                                        <td>{{ $row->subroutes->name ?? '' }}
                                            {{-- @php
                                            dump($row->image);
                                        @endphp --}}
                                        </td>
                                        @php
                                            if ($row->active == 1) {
                                                $active_status = 'Activate';
                                            } elseif ($row->active == 2) {
                                                $active_status = 'Activate Request' . ((Auth::user()->id  != $row->status_user_id) ? '('. $row->status_username .')' : '');
                                            } elseif ($row->active == 3) {
                                                $active_status = 'Deactivate Request' . ((Auth::user()->id  != $row->status_user_id) ? '('. $row->status_username .')' : '');
                                            } else {
                                                $active_status = 'Deactivate';
                                            }
                                        @endphp
                                        <td>{{ $active_status }}</td>

                                    </tr>
                                @endforeach
                        </table>

                        {{-- <div>
                            Total Records: {{ $shop->total() }}
                        </div>

                        <div>
                            Total Pages: {{ $shop->lastPage() }}
                        </div>
                        <div style="float: right">
                        {!! $shop->appends(request()->query())->links() !!} --}}

                        <div class="generateshops" style="text-align: right">
                            <button id="bulk-status-check-btn" disabled data-url="{{ route('shop.status_request_post') }}"
                                class="btn btn-primary btn-xs bulk-status-check-btn" data-approve_reject="1" data-status="{{$status_request}}" data-text="Are you sure you want to make changes" type="button" >Approve</button>
                            <button id="bulk-status-check-btn" disabled data-url="{{ route('shop.status_request_post') }}"
                                class="btn btn-danger btn-xs bulk-status-check-btn" data-approve_reject="0" data-status="{{$status_request}}" data-text="Are you sure you want to make changes" type="button" >Reject</button>
                        </div>
                    </div>
            @endif
        </div>
    </div>
</div>
</div>


<script>
    // Bind event outside of AJAX call
    $(document).ready(function() {
        // $('.launcher').on('click', function() {
        //     showModal($(this).data('url'), $(this).data('title'));
        // });
        // // Close modal when clicking on close button or outside the modal
        // $('#showModal').on('click', closeModal);
        // $('#showModal .modal-dialog').on('click', function(event) {
        //     event.stopPropagation(); // Prevent closing when clicking on modal content
        // });
        // $('#showModal .modal-close').on('click', closeModal);


        var checked = [];
        $(document).on('change', '.bulk-status-check', function() {
            $('.bulk-status-check-btn').prop('disabled', false);
            if (this.checked) {
                // alert('checked');
                checked.push($(this).val());
                console.log(checked);
            } else {
                // alert('no checked');
                checked.pop();
                console.log(checked);
            }

            if (checked.length > 0) {
                $('.bulk-status-check-btn').prop('disabled', false);
            } else {
                $('.bulk-status-check-btn').prop('disabled', true);
            }


        });


        $(document).on('change', '.bulk-status-check-all', function() {
            if (this.checked) {
                $('.bulk-status-check-btn').prop('disabled', false);
                $('.bulk-status-check').prop('checked', true);
                $(".bulk-status-check:checked").each(function() {
                    checked.push($(this).val());
                });
            } else {
                checked = [];
                $('.bulk-status-check').prop('checked', false);
                $('.bulk-status-check-btn').prop('disabled', true);
            }

        });

        // $('#bulk-status-check-btn').on('click', function() {
        //     status = $('#status_request_data').val();
        //     $.ajax({
        //         type: 'post',
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         url: $(this).data('url'),
        //         data: {
        //             'checked_records': checked,
        //             'status' : status
        //         },
        //         success: function(response, textStatus, xhr) {
        //             if (response.status) {
        //                 alert(response.msg);
        //                 location.reload();
        //             }
        //         }
        //     });
        // });






     // Activate Product
     $(document).on('click', '.bulk-status-check-btn', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        var text = $(this).data('text');
        // var status = $('#status_request_data').val();
        var status = $(this).data('status');
        var approve_reject = $(this).data('approve_reject');
        // console.log(status , approve_reject , remarks);

        if (status == 3) {
            heading = 'Deactivated!';
        }
        else
        {
            heading = 'Activated!'
        }
        if (approve_reject == 0) {
            heading = 'Rejected!'
        }
        Swal.fire({
            title: 'Are you sure?',
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: heading,
            html: `
                <textarea id="remarks-input" class="swal2-textarea" placeholder="Enter your remarks"></textarea>
            `, // Add a textarea for remarks
            preConfirm: () => {
                const remarks = Swal.getPopup().querySelector('#remarks-input').value;
                if (!remarks) {
                    Swal.showValidationMessage('Remarks are required!');
                }
                return remarks; // Return remarks for use in the AJAX call
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const remarks = result.value; // Get the remarks value
                $.ajax({
                    url: url,
                    type: 'POST', // or 'GET', depending on your backend setup
                    data: {
                        _token: '{{ csrf_token() }}' ,// Include CSRF token for security
                        status: status,
                        checked_records: checked,
                        remarks: remarks,
                        approve_reject: approve_reject,
                    },
                    success: function(response) {
                        Swal.fire(
                            heading,
                            response.success,
                            'success'
                        ).then(() => {
                            get_ajax_data(); // Reload the page to see the changes
                        });
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            'There was a problem activating.',
                            'error'
                        );
                    }
                });
            }
        });
    });

    });
</script>
