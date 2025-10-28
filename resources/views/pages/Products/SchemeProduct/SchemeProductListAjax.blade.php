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
                    @can('Scheme_Product_Edit')
                        <a href="{{ route('scheme_product.edit', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item">Edit</a>
                    @endcan
                    @can('Scheme_Product_Delete')
                    <a href="javascript:void(0);" data-url="{{ route('scheme_product.destroy', $row->id) }}" id="delete-user" class="dropdown-item_sale_order_list dropdown-item" >Delete</a>
                    @endcan
                    @if ($row->active == 0)
                        @can('Scheme_Product_Activate')
                            <a href="javascript:void(0);" data-text="You want to activate this Scheme" data-url="{{ route('scheme_product.active', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item" id="active-record">Activate</a>
                        @endcan
                    @else
                        @can('Scheme_Product_Deactivate')
                            <a href="javascript:void(0);" data-text="You want to deactivate this Scheme" data-url="{{ route('scheme_product.inactive', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item" id="inactive-record">Deactivate</a>
                        @endcan
                    @endif
                </div>
            </div>
        </td>

    </tr>
@endforeach
