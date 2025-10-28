<?php
use App\Models\ShopVisit;
use App\Models\SaleOrder;
use App\Models\Route;
use App\Models\Shop;
?>
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
                <strong>Laziza International</strong><br>
                <strong>Sales Return Report</strong><br>
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
                <th>Product</th>
                <th>Packing</th>
                <th>Qty</th>
                <th>Return Value</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_qty = 0;
                $total = 0;
            @endphp
            @foreach ($data as $key => $row)
                @php
                    $total_qty += $row->quantity;
                    $total += $row->total;
                @endphp
                <tr>
                    <td>{{ ++$key }}</td>
                    <td>{{ $row->product_name }}</td>
                    <td>{{ $row->packing }}</td>
                    <td>{{ $row->quantity }}</td>
                    <td>{{ number_format($row->total, 2) }}</td>
                </tr>
            @endforeach
            <tfoot>
                <tr style="background-color: lightgray;font-size: large;font-weight: bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ number_format($total_qty, 2) }}</td>
                    <td>{{ number_format($total, 2) }}</td>
                </tr>
            </tfoot>
        </tbody>
    </table>
</div>
