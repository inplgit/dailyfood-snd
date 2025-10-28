@foreach ($type as $key => $row)
    <tr>
        <td>{{ ++$key }}</td>
        <td>{{ $row->type_name }}</td>
        <td>{{ $row->username }}</td>
        <td>
            <div class="dropdown">
                <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                <div class="dropdown-menu dropdown-menu_sale_order_list" aria-labelledby="dropdownMenuButton">   
                    @can('Product_Type_Edit')
                        <a href="{{ route('type.edit', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item">Edit</a>
                    @endcan
                    @can('Product_Type_Delete')
                        <a href="{{ route('type.destroy', $row->id) }}" id="delete-user" class="dropdown-item_sale_order_list dropdown-item" href="#">Delete</a>
                    @endcan
                </div>
            </div
        </td>

    </tr>
@endforeach