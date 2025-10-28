<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>
@php
    if (Auth::user()->hasAnyRole(['CEO','Super Admin'])) {
        $data = $master->get_tso_distribuor_wise_check();
    }
    else {
        $data = $master->get_tso_distribuor_wise_check()->where('active' ,'!=' ,0);
    }
    $count = 1;
@endphp
@foreach ($data as $key => $row)
    <tr class="text-center">
        <td>{{ $count++ }}</td>
        <td>{{ $row->tso_code ?? '' }}</td>
        <td>{{ $row->name ?? '' }}</td>
        <td>{{ $row->designation->name ?? '' }}</td>
        <td>{{ $row->cities->name ?? '' }}</td>
        <td>{{ $row->cnic ?? '-' }}</td>
        <td>{{ $row->phone ?? '' }}</td>

        <td>{{ $row->distributor->distributor_name ?? '' }}</td>
        {{-- <td>{{ $row->user->email ?? '' }}</td> --}}
        <td>{{ $row->user->email ?? '' }}</td>

        @php
            if ($row->active == 0) {
                $status = 'In Active';
            }
            elseif ($row->active == 1) {
                $status = 'Active';
            }
            elseif ($row->active == 2) {
                $status = 'Activate Request ' .((Auth::user()->id  != $row->status_user_id) ? '('. $row->status_username .')' : '');
            }
            else {
                $status = 'Deactivate Request ' .((Auth::user()->id  != $row->status_user_id) ? '('. $row->status_username .')' : '');
            }
        @endphp
        <td>{{$status}}</td>
        <td>{{ $row->date_of_join ?? '-' }}</td>
        <td>{{ $row->date_of_leaving ?? '-' }}</td>
        <td class="export-hidden">
            <!-- <div>
                @can('TSO_Edit')
                    <a href="{{ route('tso.edit', $row->id) }}" class="fas fa-edit"></a>
                @endcan
                @can('TSO_Delete')

                        @if($row->status==1)
                        <i data-url="{{ route('tso.destroy', $row->id) }}" id="delete-user" class="far fa-trash-alt"></i>
                        @else
                        <i data-url="{{ route('tso.destroy', $row->id) }}" id="delete-user" class="fas fa-undo"></i>
                        @endif
                @endcan
                @can('TSO_Import_Shop')
                    <a href="{{ route('tso.import_shops', $row->id) }}" class="fas fa-file"></a>
                @endcan
            </div> -->
            <div class="dropdown">
                <i class="fa-solid fa-ellipsis-vertical dropdown-toggle action_cursor" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                <div class="dropdown-menu dropdown-menu_sale_order_list" aria-labelledby="dropdownMenuButton">
                <a href="{{ route('tso.viewProfile',$row->id) }}" class="dropdown-item_sale_order_list dropdown-item">View</a>

                    @can('TSO_Edit')
                        <a href="{{ route('tso.edit', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item">Edit</a>
                    @endcan
                    {{-- @can('TSO_Delete')

                    @if($row->status==1)
                    <a href="javascript:void(0);" data-url="{{ route('tso.destroy', $row->id) }}" id="delete-user" class="dropdown-item_sale_order_list dropdown-item">Delete</a>
                    @else
                    <a href="javascript:void(0);" data-url="{{ route('tso.destroy', $row->id) }}" id="delete-user" class="dropdown-item_sale_order_list dropdown-item" href="#">Undo</a>
                    @endif
                    @endcan --}}
                    @can('TSO_Import_Shop')
                    <a href="{{ route('tso.import_shops', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item">File</a>
                    @endcan
                    @if ($row->active == 0)
                        @can('TSO_Activate')
                            <a href="javascript:void(0);" data-text="You want to activate this TSO" data-url="{{ route('tso.active', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item" id="active-record">Activate</a>
                        @endcan
                    @else
                        @can('TSO_Deactivate')
                            <a href="javascript:void(0);" data-text="You want to deactivate this TSO" data-url="{{ route('tso.inactive', $row->id) }}" class="dropdown-item_sale_order_list dropdown-item" id="inactive-record">Deactivate</a>
                        @endcan
                    @endif
                </div>
            </div>
        </td>
    </tr>
@endforeach
