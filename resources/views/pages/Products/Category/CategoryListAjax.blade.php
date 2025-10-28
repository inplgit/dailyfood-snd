@foreach ($category as $key => $row)
    <tr>
        <td>{{ ++$key }}</td>
        <td>{{ $row->name }}</td>
        <td>{{ $row->username }}</td>
        <td>
            <div class="dropdown">
                <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                <div class="dropdown-menu dropdown-menu_sale_order_list" aria-labelledby="dropdownMenuButton">   
                    @can('Category_Edit')
                        <a href="{{ url('product/category/' . $row->id . '/edit') }}" class="dropdown-item_sale_order_list dropdown-item">Edit</a>
                    @endcan
                    @can('Category_Delete')
                        <a href="{{ route('category.destroy', $row->id) }}" id="delete-user" class="dropdown-item_sale_order_list dropdown-item" href="#">Delete</a>
                    @endcan
                </div>
            </div
        </td>
    </tr>
@endforeach
