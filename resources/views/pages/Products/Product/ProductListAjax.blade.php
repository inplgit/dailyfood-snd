@php
    use App\Helpers\MasterFormsHelper;
    use App\Models\Attendence;

    $master = new MasterFormsHelper();
@endphp

@foreach ($product as $key => $row)
    <tr class="text-center">
        <td>{{ ++$key }}</td>
        <td>{{ $row->category->name ?? '' }}</td>
        <td>{{ $row->product_name ?? '' }}</td>
        <td>{{ $row->Brand->brand_name ?? '' }}</td>
        <td>{{ $row->ProductType->type_name ?? '' }}</td>
         <td>{{ MasterFormsHelper::product_id_get_uom($row->id) ?? '' }}</td>
        
 	<td>{{ number_format($master->get_product_price_item($row->id) ?? 0, 0) }}</td>

        <td>
            <div class="dropdown">
                <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                <div class="dropdown-menu dropdown-menu_sale_order_list" aria-labelledby="dropdownMenuButton">
                    @can('Product_Edit')
                        <a href="{{ route('product.edit', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item">Edit</a>
                    @endcan
                    @can('Product_Delete')
                        <a href="javascript:void(0);" data-url="{{ route('product.destroy', $row->id) }}" id="delete-user" class="dropdown-item_sale_order_list dropdown-item" href="#">Delete</a>
                    @endcan
                </div>
            </div>
        </td>

    </tr>
@endforeach
