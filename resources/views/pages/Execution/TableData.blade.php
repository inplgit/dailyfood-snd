@foreach ($sales as $key => $row)
    <tr class="text-center">
        <td><input {{ $row->excecution ? 'checked' : '' }} type="checkbox" value="{{ $row->id }}"
                class="bulk-execution-check" id=""></td>
        <td>{{ ++$key }}</td>
        <td>{{ $row->invoice_no }}</td>
        <td>{{ date("d-m-Y", strtotime($row->dc_date))  }}</td>
       <!-- <td>{{ $row->delivery_date ? date("d-m-Y", strtotime($row->delivery_date)) : 'N/A' }}</td> -->
        <td>{{ $row->distributor->distributor_name }}</td>
        <td>{{ $row->tso->name }}</td>
        <td>{{ $row->tso->cities->name ?? '' }}</td>
        <td>{{ $row->shop->company_name }}</td>
        <td>

            {{ $row->excecution ? 'YES' : 'NO' }}
        </td>
        <td>{{ $row->total_amount }}</td>
        <td>
            <div class="dropdown">
                <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                <div class="dropdown-menu dropdown-menu_sale_order_list" aria-labelledby="dropdownMenuButton">
                    @can('Sale_Order_Execute_View')
                        <a target="_blank" data-url="{{ route('sale.show', $row->id) }}" data-title="View Sale Execution" class="dropdown-item_sale_order_list dropdown-item launcher">View</a>
                    @endcan
                    @if (!$row->excecution)
                    @can('Sale_Order_Execute_Edit')
                        <a target="_blank" href="{{ route('sale.edit', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item">Edit</a>
                    @endcan
                      <!-- @can('Sale_Order_Execute_Delete')
                        <a  href="javascript:void(0);" data-url="{{ route('sale.destroy', $row->id) }}"  id="delete-user" class="dropdown-item_sale_order_list dropdown-item" href="#">Delete</a>
                    @endcan -->

                    @can('Sale_Order_Execute_Delete')
    <a href="javascript:void(0);" 
       data-id="{{ $row->id }}" 
       class="dropdown-item_sale_order_list dropdown-item delete-record">
       Delete
    </a>
@endcan
                    @endif
                </div>
            </div>
        </td>
    </tr>
@endforeach

<script>
// Remove the duplicate $(document).ready() wrapper
$(document).ready(function() {
    // Delete record functionality - should only be bound once
    $(document).off('click', '.delete-record').on('click', '.delete-record', function(e) {
        e.stopPropagation(); // Prevent event bubbling
        
        if (confirm('Are you sure you want to delete this record?')) {
            var recordId = $(this).data('id');
            var $row = $(this).closest('tr');
            
            $.ajax({
                url: "{{ route('sale.destroy', '') }}/" + recordId,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    // Remove the row from table
                    $row.fadeOut('slow', function() {
                        $(this).remove();
                    });
                    
                    // Show success message
                    alert(response.success);
                },
                error: function(xhr) {
                    alert('Error deleting record');
                    console.error(xhr.responseText);
                }
            });
        }
    });

    // Modal functions (keep these separate)
    function showModal(url, title) {
        var $modal = $('#showModal');
        $.ajax({
            url: url,
            method: 'GET',
            success: function(res) {
                $modal.find('.modal-body').html(res);
                $modal.find('.modal-title').text(title);
                openModal();
            },
            error: function(xhr, status, error) {
                console.error("Error loading content:", error);
            }
        });
    }

    function openModal() {
        $('#showModal').fadeIn();
    }

    function closeModal() {
        $('#showModal').fadeOut();
    }

    $('.launcher').on('click', function(e) {
        e.preventDefault();
        showModal($(this).data('url'), $(this).data('title'));
    });

    $('#showModal').on('click', closeModal);
    $('#showModal .modal-dialog').on('click', function(event) {
        event.stopPropagation();
    });
    $('#showModal .modal-close').on('click', closeModal);
});


</script>

