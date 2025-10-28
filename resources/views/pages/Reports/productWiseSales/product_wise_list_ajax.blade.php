<?php

use App\Models\ShopVisit;
use App\Models\SaleOrder;
use App\Models\Route;
use App\Models\Shop;
use App\Helpers\MasterFormsHelper;

$master = new MasterFormsHelper();
?>
<style>
    /* .dates-info-head {
    padding: 10px;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    margin-bottom: 10px;
}
.table-responsive .dates-info-head {
    overflow: visible !important;
} */
</style>

<div class="table-responsive printBody">

@if (isset($from) && isset($to))
    @php
        $distributorName = $distributor_id ? \App\Models\Distributor::find($distributor_id)->distributor_name : 'All';
        $tsoName = $tso_id ? \App\Models\TSO::find($tso_id)->name : 'All';
    @endphp

    <div class="dates-info-head text-center mb-3">
        <p class="mb-0">
            <strong>Laziza International</strong><br>
            <strong>Product Wise Sales Report</strong><br>
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
                <th>QTY </th>
                <th>TP</th>
                <th>Sales (W/O disc) </th>
                <th>Discount </th>
                <th>Sales Value </th>
            </tr>
        </thead>
        <tbody>
            @php
                $grand_total_amount = 0;
                $grand_total_product_discount = 0;
                $grand_total_amount_with_discount = 0;

                $total_packing = 0;
                $total_qty = 0;
                $total_tp = 0;
                $total_ctn_qty = 0;
                $total_val = 0;
            @endphp
            @foreach ($data as $row)
                @php
                    $product_data = $master->get_product_by_id($row->product_id);

                    $ctn_qty = 0;

                    $get_qty = '';
                    $product_price = '';
                    foreach (MasterFormsHelper::get_product_price($row->product_id) as $k => $productPrice) {
                        $qty = MasterFormsHelper::get_sale_qty2(
                            $from,
                            $to,
                            $row->product_id,
                            $row->flavour_id,
                            $productPrice->uom_id,
                            null,
                            $distributor_id,
                            null,
                        );
                        $product_discount = MasterFormsHelper::get_sale_product_discount(
                            $from,
                            $to,
                            $row->product_id,
                            $row->flavour_id,
                            $productPrice->uom_id,
                            null,
                            $distributor_id,
                            1,
                        );

                        $uom_name = $master->uom_name($productPrice->uom_id);
                        if ($qty > 0) {
                            $get_qty .= ($get_qty ? ' , ' : '') . number_format($qty);
                            $grand_total_qty[$productPrice->uom_id] = isset($grand_total_qty[$productPrice->uom_id])
                                ? $grand_total_qty[$productPrice->uom_id] + $qty
                                : $qty;
                            if ($productPrice->uom_id != 7) {
                                $ctn_qty += $qty / ($productPrice->pcs_per_carton ?? 1);
                            } else {
                                $ctn_qty += $qty;
                            }
                        }
                        $total_ctn_qty += $ctn_qty;

                        $product_price .= ($product_price ? ' , ' : '') . number_format($productPrice->trade_price, 2);
                    }
                @endphp
                <tr class="text-center">
                    <td>{{ $loop->iteration }}</td>
                    <!-- <td>{{ $row->name }}</td>
                <td>{{ $row->distributor_name }}</td>
                <td>{{ $row->cnic }}</td> -->
                    <td>{{ $row->product_name }}</td>
                    <td>{{ $row->carton_size ?? '--' }}</td>

                    <!-- <td>{{ $master->get_flavour_name($row->flavour_id) }}</td> -->

                    <td>{{ number_format($row->qty, 2) }}</td>
                    <td>{{ number_format($row->rate, 2) }}</td>
                    <td>{{ number_format($row->total, 2) }}</td>
                    <td>{{ number_format($row->discount_amount, 2) }}</td>
                    <td>{{ number_format($row->total - $row->discount_amount, 2) }}</td>

                </tr>
                @php
                    $total_packing += $row->carton_size;
                    $total_qty += $row->qty;
                    $total_tp += $row->rate;
                    $grand_total_amount += $row->total;
                    $grand_total_product_discount += $row->discount_amount;
                    $grand_total_amount_with_discount += $row->total - $row->discount_amount;
                @endphp
            @endforeach
            <tr style="background-color: lightgray;font-size: large;font-weight: bold" class="text-center">
                <td>Total</td>
                <td></td>
                <td>{{ number_format($total_packing, 2) }}</td>
                <td>{{ number_format($total_qty, 2) }}</td>
                <td>{{ number_format($total_tp, 2) }}</td>
                <td>{{ number_format($grand_total_amount, 2) }}</td>
                <td>{{ number_format($grand_total_product_discount, 2) }}</td>
                <td>{{ number_format($grand_total_amount_with_discount, 2) }}</td>
            </tr>

        </tbody>
    </table>
</div>
