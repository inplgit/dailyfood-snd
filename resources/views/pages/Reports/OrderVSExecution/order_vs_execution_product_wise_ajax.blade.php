<style>
    .table-responsive .table-bordered th,
    .table-responsive .table-bordered td {
        vertical-align: middle;
        text-align: left;
        padding: 8px;
        font-size: 12px;
        white-space: nowrap;
        /* Prevent text wrapping */
    }

    .userlittab>thead>tr>td,
    .userlittab>tbody>tr>td,
    .userlittab>tfoot>tr>td {
        vertical-align: inherit !important;
        text-align: left !important;
        padding: 5px 5px !important;
        font-size: 11px !important;
    }

    .dates-info-head p {
        text-align: center;
        margin-bottom: 20px;
        color: #000 !important;
    }

    strong {
        font-weight: bold !important;
        color: #000 !important;
    }
</style>

<div class="table-responsive printBody">
    @if (isset($from) && isset($to))
        @php
            $cityName = $city ? \App\Models\City::find($city)->name : 'All';
            $distributorName = $distributor_id
                ? \App\Models\Distributor::find($distributor_id)->distributor_name
                : 'All';
            $tsoName = $tso_id ? \App\Models\TSO::find($tso_id)->name : 'All';
        @endphp

        <div class="dates-info-head">
            <p>
                <strong>Laziza International</strong><br>
                <strong>Order vs Execution (Product Wise)</strong><br>
                <b>From:</b> {{ \Carbon\Carbon::parse($from)->format('d-M-Y') }} |
                <b>To:</b> {{ \Carbon\Carbon::parse($to)->format('d-M-Y') }} |
                <b>City:</b> {{ $cityName }} |
                <b>Distributor:</b> {{ $distributorName }} |
                <b>TSO:</b> {{ $tsoName }}
            </p>
        </div>
    @endif

    <table class="table table-bordered filterTable">
        <thead>
            <tr class="text-center">
                <th>Product</th>
                <th>Packing</th>
                <th>Order QTY</th>
                <th>Executed QTY</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_packing_qty = 0;
                $total_order_qty = 0;
                $total_execution_qty = 0;
            @endphp

            @foreach ($data as $key => $row)
                @php
                    $total_packing_qty += $row->packing_size;
                    $total_order_qty += $row->order_qty;
                    $total_execution_qty += $row->execution_qty;
                @endphp
                <tr>
                    <td>{{ $row->product_name }}</td>
                    <td>{{ $row->packing_size ?? 0 }}</td>
                    <td>{{ $row->order_qty }}</td>
                    <td>{{ $row->execution_qty }}</td>
                </tr>
            @endforeach

            <tr style="background-color: lightgray; font-size: large; font-weight: bold">
                <td class="text-left">Total</td>
                <td>{{ number_format($total_packing_qty, 2) }}</td>
                <td>{{ number_format($total_order_qty, 2) }}</td>
                <td>{{ number_format($total_execution_qty, 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>
