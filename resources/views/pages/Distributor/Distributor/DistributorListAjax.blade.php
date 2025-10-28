<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>
@foreach ($master->get_all_distributor_user_wise() as $key => $row)
    @php
        $level = explode('-', $row->distributor_sub_code);
        $level = count($level);
    @endphp
    <tr>
        <td>{{ ++$key }}</td>
        <td>{{ $row->distributor_code }}</td>
        <td @if ($level == 1) style="font-weight: bold" @endif>
            @if ($level == 2)
                &emsp;
            @elseif($level == 3)
                &emsp;&emsp;
            @endif{{ $row->distributor_name }}
        </td>
        <td>{{ $row->contact_person }}</td>
        <td>{{ $row->phone }}</td>
        <td>{{ $row->city }}</td>
        <td>{{ $row->location_latitude }}</td>
        <td>{{ $row->location_longitude }}</td>

        <td>{{ $master->get_status_value()[$row->status] }}</td>
        {{-- <td>{{ $row->zone->zone_name }}</td> --}}

        <td class="export-hidden">
            <div class="dropdown">
                <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                <div class="dropdown-menu dropdown-menu_sale_order_list" aria-labelledby="dropdownMenuButton">   
                    @can('Distributor_Edit')
                        <a href="{{ route('distributor.edit', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item">Edit</a>
                    @endcan
                     @can('Distributor_Delete')
                        <form action="{{ route('distributor.destroy1', $row->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item_sale_order_list dropdown-item" style="background: none; border: none;">Delete</button>
                        </form>
            

                    @endcan
                </div>
            </div
        </td>

    </tr>
@endforeach
