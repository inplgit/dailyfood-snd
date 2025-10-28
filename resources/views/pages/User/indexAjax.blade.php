@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();

@endphp
@foreach ($data as $key => $row)
    @php
        $type = $row->type->type;
    @endphp
    <tr id="{{ $row->id }}">
        <td>{{ ++$key }}</td>
        <td>{{ $row->name }}</td>
        <td>{{ $row->email }}</td>
        <td>{{ $type }}</td>
        <td>
            <div class="dropdown">
                <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                <div class="dropdown-menu dropdown-menu_sale_order_list" aria-labelledby="dropdownMenuButton">
                @if (!$row->tso)
                    <div>
                        @if(!empty($row->id))
                            <a href="{{ route('user.viewProfile', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item">View</a>
                            {{-- <a href="{{ route('user.viewProfile', ['id' => $row->id]) }}" class="dropdown-item_sale_order_list dropdown-item">View</a> --}}
                        @endif
                        @can('User_List_Edit')
                            <a href="{{ route('users.edit', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item">Edit</a>
                        @endcan
                        @can('User_List_Delete')
                            <a data-url="{{ route('users.destroy', $row->id) }}" id="delete-user" class="dropdown-item_sale_order_list dropdown-item" href="#">Delete</a>
                        @endcan
                    </div>
                    @endif
                </div>
            </div>
        </td>
    </tr>
@endforeach
