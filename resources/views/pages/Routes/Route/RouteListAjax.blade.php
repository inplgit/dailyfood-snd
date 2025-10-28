@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
@endphp
@foreach ($master->get_route_distribuor_wise() as $key => $row)
    <tr>
        <td>{{ ++$key }}</td>
        <td>{{ $row->route_name }}</td>
        <td>{{ $row->tso->cities->name ?? '' }}</td>
        <td>{{ $row->distributor->distributor_name }}</td>
        <td>{{ $row->tso->name ?? '' }}</td>
        @php
            // dump($row->RouteDay->toArray());
            $days = $row->RouteDay ? array_column($row->RouteDay->toArray(), 'day') : '';
            $days = $days ? implode(',',$days) : '';
        @endphp
        <td>{{ $days }}</td>
        {{-- <td>{{ $row->day }}</td> --}}

        <td class="export-hidden">
            <!-- <div>
                @can('Route_Edit')
                    <a href="{{ route('route.edit', $row->id) }}" class="btn btn-primary btn-sm">Edit</a>
                @endcan
                @can('Route_Delete')
                    <button type="button" id="delete-user" data-url="{{ route('route.destroy', $row->id) }}"
                        class="btn btn-danger btn-sm">Delete</button>
                @endcan
            </div> -->
            <div class="dropdown">
                <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                <div class="dropdown-menu dropdown-menu_sale_order_list" aria-labelledby="dropdownMenuButton">
                    @can('Route_Edit')
                        <a href="{{ route('route.edit', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item">Edit</a>
                    @endcan
                    @can('Route_Delete')
                        <a href="javascript:void(0);" data-url="{{ route('route.destroy', $row->id) }}" id="delete-user" class="dropdown-item_sale_order_list dropdown-item" href="#">Delete</a>
                    @endcan
                </div>
            </div
        </td>

    </tr>
@endforeach
