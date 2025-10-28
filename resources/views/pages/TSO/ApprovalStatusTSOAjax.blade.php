<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>

{{-- @php
    dd($master->get_tso_distribuor_wise()->where('active' , $status_request));
@endphp --}}
@foreach ($master->get_tso_distribuor_wise()->where('active' , $status_request) as $key => $row)
    <tr class="text-center">
        <td><input type="checkbox" name=""
                value="{{ $row->id }}" class="bulk-status-check" id="">
        </td>
        <td>{{ ++$key }}</td>
        <td>{{ $row->tso_code ?? '' }}</td>
        <td>{{ $row->name ?? '' }}</td>
        <td>{{ $row->designation->name ?? '' }}</td>
        <td>{{ $row->cities->name ?? '' }}</td>
        <td>{{ $row->cnic ?? '-' }}</td>
        <td>{{ $row->phone ?? '' }}</td>

        <td>{{ $row->distributor->distributor_name }}</td>
        {{-- <td>{{ $row->user->email ?? '' }}</td> --}}
        <td>{{ $row->user->email ?? '' }}</td>

        @php
            if ($row->active == 0) {
                $status = 'In Active';
            } elseif ($row->active == 1) {
                $status = 'Active';
            } elseif ($row->active == 2) {
                $status = 'Activate Request ' . ((Auth::user()->id  != $row->status_user_id) ? '('. $row->status_username .')' : '');
            } else {
                $status = 'Deactivate Request ' .((Auth::user()->id  != $row->status_user_id) ? '('. $row->status_username .')' : '');
            }
        @endphp
        <td>{{ $status }}</td>
        <td>{{ $row->date_of_join ?? '-' }}</td>
        <td>{{ $row->date_of_leaving ?? '-' }}</td>

    </tr>
@endforeach

<script>
    // Bind event outside of AJAX call
    $(document).ready(function() {


        function checked_length()
        {
            if (checked.length > 0) {
                $('.bulk-status-check-btn').prop('disabled', false);
            } else {
                $('.bulk-status-check-btn').prop('disabled', true);
            }
        }

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

            checked_length();
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

            checked_length();

        });



        // Activate Product
        $(document).on('click', '.bulk-status-check-btn', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var text = $(this).data('text');
            var status = "{{$status_request}}";
            var approve_reject = $(this).data('approve_reject');

            if (status == 3) {
                heading = 'Deactivated!';
            } else {
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
                            _token: '{{ csrf_token() }}', // Include CSRF token for security
                            status: status,
                            checked_records: checked,
                            remarks: remarks,
                            approve_reject: approve_reject,
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    heading,
                                    response.success,
                                    'success'
                                ).then(() => {
                                    get_ajax_data(); // Reload the page to see the changes
                                    checked_length();
                                });
                            }
                            else
                            {
                                Swal.fire(
                                    'Error!',
                                    response.catchError,
                                    'error'
                                );
                            }
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

        checked_length();
    });
</script>
