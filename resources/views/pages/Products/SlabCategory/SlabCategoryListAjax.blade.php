@foreach ($slab_category as $key => $row)
    <tr class="text-center">
        <td>{{ ++$key }}
        </td>
        <td>{{ $row->slab_name ?? '' }}</td>
        <td>{{ $row->description ?? '---' }}</td>
        <td>{{ $row->active == 1 ? 'Activate' :  'Deactivate' }}</td>
        <td>{{ $row->date ?? '' }}</td>
        <td>
            <div class="dropdown">
                <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                <div class="dropdown-menu dropdown-menu_sale_order_list" aria-labelledby="dropdownMenuButton">
                  
                        <a href="{{ route('slab_category.edit', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item">Edit</a>
                  
                    <a href="javascript:void(0);" data-url="{{ route('slab_category.destroy', $row->id) }}" id="delete-user" class="dropdown-item_sale_order_list dropdown-item" >Delete</a>

                      
                </div>
            </div>
        </td>

    </tr>
@endforeach
