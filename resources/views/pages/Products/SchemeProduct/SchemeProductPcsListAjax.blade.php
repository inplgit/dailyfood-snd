@foreach ($scheme_product as $key => $row)
    <tr class="text-center">
        <td>{{ ++$key }}
        </td>
        <td>{{ $row->scheme_name ?? '' }}</td>
        <td>{{ $row->description ?? '---' }}</td>
        <td>{{ $row->active == 1 ? 'Activate' :  'Deactivate' }}</td>
        <td>{{ $row->date ?? '' }}</td>
        <td>
            <div class="dropdown">
                <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                <div class="dropdown-menu dropdown-menu_sale_order_list" aria-labelledby="dropdownMenuButton">
                  
                        <a href="{{ route('store_pcse_edit_pcs', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item">Edit</a>
                   
                    <a href="javascript:void(0);" data-url="{{ route('destroy_pcs', $row->id) }}" id="delete-user" class="dropdown-item_sale_order_list dropdown-item" >Delete</a>
                  
                        
                </div>
            </div>
        </td>

    </tr>
@endforeach

<script>

$(document).on('click', '#delete-user', function () {
    var url = $(this).data('url');
    if (confirm('Are you sure?')) {
        $.ajax({
            url: url,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                alert(response.success);
                location.reload();
            },
            error: function () {
                alert('Error deleting record.');
            }
        });
    }
});

</script>

