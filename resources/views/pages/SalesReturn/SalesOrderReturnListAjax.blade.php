<?php
use App\Helpers\MasterFormsHelper;

?>
@foreach ($sales_return as $key => $row)
    <tr>
        @if (isset($excution))
            <td><input type="checkbox" class="bulk-execution-check" value="{{ $row->id }}" name="id[]" /></td>
        @endif
        <td>{{ ++$key }}</td>
        <td>{{ strtoupper($row->return_no) }}</td>

        <td>{{ strtoupper($row->distributor->distributor_name ?? 'N/A') }}</td>
        <td>{{ strtoupper($row->tso->name ?? 'N/A') }}</td>
        <td>{{ strtoupper($row->shop->company_name ?? 'N/A') }}</td>
        <td>{{ $row->excute ? 'Yes' : 'No' }}</td>
        <td>
            <div class="dropdown">
                <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                <div class="dropdown-menu dropdown-menu_sale_order_list" aria-labelledby="dropdownMenuButton">
                    <a target="_blank" data-url="{{ route('sales_return.sales_return_list_shop', $row->id) }}" data-title="View Sale Return" class="dropdown-item_sale_order_list dropdown-item launcher">View</a>
                    <!-- @can('Sale_Return_Execute_Edit', 'Sales_Return_Edit')
                        <a href="{{ route('sales_return.edit', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item">Edit</a>
                    @endcan -->
                    @can('Sale_Return_Execute_Delete', 'Sales_Return_Delete')
                        <a href="{{ route('sales_return.destroy', $row->id) }}" id="delete-user" class="dropdown-item_sale_order_list dropdown-item">Delete</a>
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
