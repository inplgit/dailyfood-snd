<div class="table-responsive printBody">
    @if (isset($from) && isset($to))
        @php
            $distributorName = $distributor_id
                ? \App\Models\Distributor::find($distributor_id)->distributor_name
                : 'All';
            $tsoName = $tso_id ? \App\Models\TSO::find($tso_id)->name : 'All';
        @endphp
        <div class="dates-info-head text-center">
            <p>
                <strong>Daily Food</strong><br>
                <strong>{{ $type === 'time' ? 'Shops Time Spent Report' : 'Shops Amount Wise Report' }}</strong><br>
                <b>From:</b> {{ \Carbon\Carbon::parse($from)->format('d-M-Y') }} |
                <b>To:</b> {{ \Carbon\Carbon::parse($to)->format('d-M-Y') }} |
                <b>Distributor:</b> {{ $distributorName }} |
                <b>TSO:</b> {{ $tsoName }}
            </p>
        </div>
    @endif

    <table class="table table-bordered filterTable" id="dataTable">
        <thead class="bg-light">
            <tr class="text-center">
                <th>S.NO</th>
                <th>Distributor</th>
                <th>Shop Code</th>
                <th>Shop Name</th>
                <th>Order Booker</th>
                @if ($type === 'time')
                    <th>Total Visits</th>
                    <th>Time Spent (HH:MM:SS)</th>
                @else
                    <th>Total Orders</th>
                    <th>Total Amount</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $row)
                <tr>
                    <td>{{ ++$key }}</td>
                    <td>{{ $row->distributor_name }}</td>
                    <td>{{ $row->shop_code }}</td>
                    <td>{{ $row->shop_name }}</td>
                    <td>{{ $row->tso_name }}</td>
                    @if ($type === 'time')
                        <td class="text-center">{{ $row->total_visits }}</td>
                        <td class="text-center">{{ $row->time_spent_formatted }}</td>
                    @else
                        <td class="text-center">{{ $row->total_orders }}</td>
                        <td class="text-right">{{ number_format($row->total_amount, 2) }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: lightgray; font-weight: bold;">
                <td colspan="5" class="text-right">Total:</td>
                @if ($type === 'time')
                    <td class="text-center">{{ $totalOrders }}</td>
                    <td class="text-center">{{ $totalTimeSpent }}</td>
                @else
                    <td class="text-center">{{ $totalOrders }}</td>
                    <td class="text-right">{{ number_format($totalAmount, 2) }}</td>
                @endif
            </tr>
        </tfoot>
    </table>
</div>
