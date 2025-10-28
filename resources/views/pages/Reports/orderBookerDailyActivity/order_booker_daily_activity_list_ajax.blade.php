<?php

use App\Models\ShopVisit;
use App\Models\SaleOrder;
use App\Models\Route;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;
?>


<div class="table-responsive printBody">
    @if (isset($to))
        @php
            $distributorName = $distributor_id
                ? \App\Models\Distributor::find($distributor_id)->distributor_name
                : 'All';
            $tsoName = $tso_id ? \App\Models\TSO::find($tso_id)->name : 'All';
        @endphp

        <div class="dates-info-head text-center mb-3">
            <p class="mb-0">
                <strong>Laziza International</strong><br>
                <strong>Order Booker Daily Activity Report</strong><br>
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
                <th>Order Booker</th>
                <th>Manager</th>
                <th>Distributor</th>
                {{-- <th>Total Shop</th> --}}
                <th>Total Visit </th>
                <th>Unproductive Visits</th>
                <th>Productive Visits</th>
                <th>Executed QTY</th>
                <th>Executed Sales</th>
                <th>Shop with Sales</th>
                <th>Shop with Return (qty)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_total_visit = 0;
                $total_unproductive_visit = 0;
                $total_productive_visit = 0;
                $total_sale_orders = 0;
                $total_executed_qty = 0;
                $total_executed_sales = 0;
                $total_shop_with_return = 0;
            @endphp
            @foreach ($data as $key => $row)
                @php
                    $saleOrders = DB::table('sale_orders')
                        ->when($shop_id, function ($query, $shopId) {
                            return $query->where('shop_id', $shopId);
                        })
                        ->when($distributor_id, function ($query, $distId) {
                            return $query->where('distributor_id', $distId);
                        })
                        ->when($row->tso_id, function ($query, $tsoId) {
                            return $query->where('tso_id', $tsoId);
                        })
                        ->when($from && $to, function ($query) use ($from, $to) {
                            return $query->whereBetween('dc_date', [$from, $to]);
                        })
                        ->count();

                    // dd($row->shop_id, $distributor_id, $row->tso_id, $row->route_id, $from, $to, $saleOrders);
                    // $unproductive_visit = $row->total_shop - $row->productive_visit;
                    $totalVisit = $saleOrders + $row->visits_count;
                @endphp
                <tr>
                    <td>{{ ++$key }}</td>
                    <td>{{ $row->tso }}</td>
                    <td>{{ $row->manager_name }}</td>
                    <td>{{ $row->distributor_name }}</td>
                    {{-- <td>{{ $row->total_shop }}</td> --}}
                    <td>{{ $totalVisit }}</td>
                    <td>{{ $row->visits_count ?? 0 }}</td>
                    <td>{{ $saleOrders ?? 0 }}</td>
                    <td>{{ number_format($row->executed_qty, 2) }}</td>
                    <td>{{ number_format($row->executed_sales, 2) }}</td>
                    <td>{{ $row->productive_visit ?? 0 }}</td>
                    <td>{{ $row->shop_with_return ?? 0 }}</td>
                    @php
                        $total_total_visit += $totalVisit;
                        $total_unproductive_visit += $row->visits_count;
                        $total_sale_orders += $saleOrders;
                        $total_executed_qty += $row->executed_qty;
                        $total_executed_sales += $row->executed_sales;
                        $total_productive_visit += $row->productive_visit;
                        $total_shop_with_return += $row->shop_with_return;
                    @endphp
                </tr>
            @endforeach
            <tr style="background-color: darkgray;font-weight: bold" class="bold">
                <td>Total</td>
                {{-- <td></td> --}}
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $total_total_visit }}</td>
                <td>{{ $total_unproductive_visit }}</td>
                <td>{{ $total_sale_orders }}</td>
                <td>{{ number_format($total_executed_qty, 2) }}</td>
                <td>{{ number_format($total_executed_sales, 2) }}</td>
                <td>{{ $total_productive_visit }}</td>
                <td>{{ $total_shop_with_return }}</td>
            </tr>
        </tbody>
    </table>
</div>
