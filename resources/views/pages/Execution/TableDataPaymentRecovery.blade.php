@foreach ($receipts as $key => $row)
    <tr class="text-center">
        <td><input {{ $row->excecution ? 'checked' : '' }} type="checkbox" value="{{ $row->id }}"
                class="bulk-execution-check" id=""></td>
        <td>{{ ++$key }}</td>
        <td>{{ $row->issue_date}}</td>
        <td>{{ $row->distributor->distributor_name }}</td>
        <td>{{ $row->tso->name }}</td>
        <td>{{ $row->shop->company_name }}</td>
        <td>{{ $row->deliveryMan->name ?? '' }}</td>
        <td>{{ $row->route->route_name }}</td>
        <td>

            {{ $row->excecution ? 'YES' : 'NO' }}
        </td>
        <td>{{ $row->amount }}</td>
        <td>
            <div class="dropdown">
                <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                <div class="dropdown-menu dropdown-menu_sale_order_list" aria-labelledby="dropdownMenuButton">
                    <a target="_blank" data-url="{{ route('receipt-voucher.show', $row->id) }}" data-title="View Payment Recovery" class="dropdown-item_sale_order_list dropdown-item launcher">View</a>

                    @can('Payment_Recovery_Execute_Edit')
                    <a href="{{ route('sales_return.edit', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item">Edit</a>
                    @endcan

                    @can('Payment_Recovery_Execute_Delete')
                        <a href="{{ route('sales_return.destroy', $row->id) }}" id="delete-user" class="dropdown-item_sale_order_list dropdown-item" href="#">Delete</a>
                    @endcan
                </div>
            </div>
        </td>

    </tr>
@endforeach

<script>
 function showModal(url, title) {
        var $modal = $('#showModal'); // Define $modal within the function
        $.ajax({
            url: url,
            method: 'GET',
            success: function(res) {
                // Update modal content
                $modal.find('.modal-body').html(res);
                $modal.find('.modal-title').text(title);
                // Open modal after updating content
                openModal();
            },
            error: function(xhr, status, error) {
                // Handle errors if necessary
                console.error("Error loading content:", error);
            }
        });
    }

    // Define openModal and closeModal functions outside of showModal
    function openModal() {
        $('#showModal').fadeIn();
    }

    function closeModal() {
        $('#showModal').fadeOut();
    }

    // Bind event outside of AJAX call
    $(document).ready(function() {
        $('.launcher').on('click', function() {
            showModal($(this).data('url'), $(this).data('title'));
        });
        // Close modal when clicking on close button or outside the modal
        $('#showModal').on('click', closeModal);
        $('#showModal .modal-dialog').on('click', function(event) {
            event.stopPropagation(); // Prevent closing when clicking on modal content
        });
        $('#showModal .modal-close').on('click', closeModal);
    });


</script>
