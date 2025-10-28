<?php
use App\Helpers\MasterFormsHelper;

$master = new MasterFormsHelper();
?>

<style>
    .table-bordered>thead>tr>th,
    .table-bordered>tbody>tr>th,
    .table-bordered>tfoot>tr>th {
        vertical-align: inherit !important;
        text-align: left !important;
        padding: 5px 5px !important;
        font-size: 11px !important;
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
                <strong>Distributor Product Sales Value Report</strong><br>
                <b>From:</b> {{ \Carbon\Carbon::parse($from)->format('d-M-Y') }} |
                <b>To:</b> {{ \Carbon\Carbon::parse($to)->format('d-M-Y') }} |
                <b>City:</b> {{ $cityName }} |
                <b>Distributor:</b> {{ $distributorName }} |
                <b>TSO:</b> {{ $tsoName }}
            </p>
        </div>
    @endif

    <br>

    <table class="table table-bordered userlittab sale_older_tab filterTable">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Brand</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Sales Value</th>
                <th>Avg Rate</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 1;
                $total_qty = 0;
                $total_value = 0;
            @endphp

            @foreach ($productSales as $product)
                @php
                    $avg_rate = $product->qty > 0 ? round($product->sales_value / $product->qty) : 0;
                    $total_qty += $product->qty;
                    $total_value += $product->sales_value;
                @endphp
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>LAZIZA</td>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->qty }}</td>
                    <td>{{ number_format($product->sales_value, 2) }}</td>
                    <td>{{ number_format($avg_rate, 2) }}</td>
                </tr>
            @endforeach

            <tr style="font-weight: bold; background-color: #f0f0f0;">
                <td class="text-right">Total</td>
                <td></td>
                <td></td>
                <td>{{ number_format($total_qty, 2) }}</td>
                <td>{{ number_format($total_value, 2) }}</td>
                {{-- <td>{{ $total_qty > 0 ? number_format(round($total_value / $total_qty), 2) : 0 }}</td> --}}
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
