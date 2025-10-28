@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
@endphp
@foreach($subroutes as $key => $row)
    <tr>
        <td>{{ ++$key }}</td>
        <td>{{ $row->route->route_name }}</td>
        <td>{{ $row->name }}</td>


        <td class="export-hidden">
            <!-- <div>
                <a href="{{ route('subroutes.edit',$row->id) }}" class="btn btn-primary btn-sm">Edit</a>
                <button type="button" id="delete-user" data-url="{{ route('subroutes.destroy', $row->id) }}"  class="btn btn-danger btn-sm">Delete</button>
            </div> -->
            <div class="dropdown">
                <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                <div class="dropdown-menu dropdown-menu_sale_order_list" aria-labelledby="dropdownMenuButton">   
                        <a href="{{ route('subroutes.edit',$row->id) }}" class="dropdown-item_sale_order_list dropdown-item">Edit</a>
                        <a href="{{ route('subroutes.destroy', $row->id) }}" id="delete-user" class="dropdown-item_sale_order_list dropdown-item" href="#">Delete</a>
                </div>
            </div
        </td>

    </tr>
@endforeach


