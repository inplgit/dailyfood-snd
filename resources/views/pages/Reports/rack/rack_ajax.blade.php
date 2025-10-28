<?php
use App\Models\SaleOrder;
use Illuminate\Support\Facades\DB;
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>
<table class="table table-bordered" id="print_data">
    <thead>
        <th>S/no</th>
        {{-- <th>Month</th> --}}
        <th>Distributer</th>
        <th>TSO</th>
        <th>Rack</th>
        <th>Shop</th>
        <th>Rack Status</th>
        <th>Remarks</th>

    </thead>

    <tbody>
        @php
            $i = 1;

        @endphp
        @foreach ($racks_details as $racks_detail)

            <tr>
                <td>{{ $i++ }}</td>
                {{-- <td>  {{$month = date('M', strtotime($target->created_at));}}</td> --}}
                <td>{{ $racks_detail->distributor_name }}</td>
                <td>{{ $racks_detail->tso_name }}</td>
                <td>{{ $racks_detail->rack_code }}</td>
                <td>{{ $racks_detail->shop_name }}</td>
                <td>{{ $racks_detail->assign_status == 1 ? 'Assigned' : 'Reclaim' }}</td>
                <td>{{ $racks_detail->remarks }}</td>

            </tr>
        @endforeach
    </tbody>
</table>
