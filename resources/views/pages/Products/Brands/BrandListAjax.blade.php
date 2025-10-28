@foreach ($brand as $key => $row)
    <tr>
        <td>{{ ++$key }}</td>
        <td>{{ $row->brand_name }}</td>
        <td>{{ $row->username }}</td>
        <td>
            <div class="dropdown">
                <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                <div class="dropdown-menu dropdown-menu_sale_order_list" aria-labelledby="dropdownMenuButton">   
                    @can('Brand_Edit')
                        <a href="{{ route('brand.edit', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item">Edit</a>
                    @endcan
                    @can('Brand_Delete')
                        <a href="{{ route('brand.destroy', $row->id) }}" id="delete-user" class="dropdown-item_sale_order_list dropdown-item" href="#">Delete</a>
                    @endcan
                </div>
            </div
        </td>

    </tr>
@endforeach
