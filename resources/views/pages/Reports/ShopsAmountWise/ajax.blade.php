
<div class="table-responsive printBody">
    @if(isset($from) && isset($to))
        @php
            $distributorName = $distributor_id ? \App\Models\Distributor::find($distributor_id)->distributor_name : 'All';
            $tsoName = $tso_id ? \App\Models\TSO::find($tso_id)->name : 'All';
        @endphp

        <div class="dates-info-head text-center" >
            <p>
                <strong>Daily Food</strong><br>
                <strong>Shops Amount Wise Report</strong><br>
                <b>From:</b> {{ \Carbon\Carbon::parse($from)->format('d-M-Y') }} |
                <b>To:</b> {{ \Carbon\Carbon::parse($to)->format('d-M-Y') }} |
                <b>Distributor:</b> {{ $distributorName }} |
                <b>TSO:</b> {{ $tsoName }}
            </p>
        </div>
    @endif
    <table class="table table-bordered filterTable">
        <thead>
            <tr class="text-center">
                <th>S.NO</th>
                <th>Distributor</th>
                <th>Shop Code</th>
                <th>Shop Name</th>
                <th>Order Booker</th>
                <th>Total Orders</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $row)
                <tr>
                    <td>{{ ++$key }}</td>
                    <td>{{ $row->distributor_name }}</td>
                    <td>{{ $row->shop_code }}</td>
                    <td>{{ $row->shop_name }}</td>
                    <td>{{ $row->tso_name }}</td>
                    <td class="text-center">{{ $row->total_orders }}</td>
                    <td>{{ number_format($row->total_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total:</td>
                <td colspan="4"></td>
                <td class="text-center">{{ $totalOrders }}</td>
                <td>{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
