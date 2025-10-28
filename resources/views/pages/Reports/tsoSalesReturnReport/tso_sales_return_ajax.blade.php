
<?php
    use App\Models\ShopVisit;
    use App\Models\SaleOrder;
    use App\Models\Route;
    use App\Models\Shop;
?>
<div class="table-responsive printBody">
    @if(isset($from) && isset($to))
        @php
            $distributorName = $distributor_id ? \App\Models\Distributor::find($distributor_id)->distributor_name : 'All';
            $tsoName = $tso_id ? \App\Models\TSO::find($tso_id)->name : 'All';
        @endphp
        <div class="dates-info-head text-center" >
            <p>
                <strong>Laziza International</strong><br>
                <strong>TSO Sales Return Report</strong><br>
                <b>From:</b> {{ \Carbon\Carbon::parse($from)->format('d-M-Y') }} |
                <b>To:</b> {{ \Carbon\Carbon::parse($to)->format('d-M-Y') }} |
                {{-- <b>City:</b> {{ $cityName }} | --}}
                <b>Distributor:</b> {{ $distributorName }} |
                <b>TSO:</b> {{ $tsoName }}
            </p>
        </div>
    @endif
    <table class="table table-bordered filterTable">
        <thead>
            <tr class="text-center">
                <th>S.NO</th>
                <th>Bill #</th>
                <th>Date</th>
                <th>Shop</th>
                <th>Route</th>
                <th>Order Booker</th>
                <th>Manager</th>
                <th>Distributor </th>
                <th>Total Units</th>
            </tr>
        </thead>
        <tbody>
        @php
            $total_qty = 0;
        @endphp
            @foreach($data as $key => $row)
                @php
                $total_qty += $row->quantity;
                @endphp
                <tr>
                    <td>{{ ++$key }}</td>
                    <td>{{ $row->return_no }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->return_date)->format('d-M-Y') }}</td>
                    <td>{{ $row->shop_name }}</td>
                    <td>{{ $row->route_name }}</td>
                    <td>{{ $row->tso }}</td>
                    <td>{{ $row->user_name ?? '' }}</td>
                    <td>{{ $row->distributor_name }}</td>
                    <td>{{ $row->quantity }}</td>
                </tr>
            @endforeach
            <tfoot>
                <tr style="background-color: lightgray;font-size: large;font-weight: bold">
                    <td colspan="8" class="text-end">Total</td>
                    <td>{{ number_format($total_qty, 2) }}</td>
                </tr>
            </tfoot>
        </tbody>
    </table>
</div>
