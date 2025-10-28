<?php

use App\Models\ShopVisit;
use App\Models\SaleOrder;
use App\Models\Route;
use App\Models\Shop;
?>



<div class="table-responsive printBody">
    @if (isset($to))
        @php
            $distributorName = $distributor_id ? \App\Models\Distributor::find($distributor_id)->distributor_name : 'All';
            $tsoName = $tso_id ? \App\Models\TSO::find($tso_id)->name : 'All';
        @endphp

        <div class="dates-info-head text-center mb-3">
            <p class="mb-0">
                <strong>Laziza International</strong><br>
                <strong>Unproductive Shop List</strong><br>
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
                <th>Shop Code</th>
                <th>Shop</th>
                {{-- <th>Total Shop </th> --}}
                <th>Total Visit </th>
                <th>Unproductive Shops</th>
                <th>Route</th>
                <th>Order Booker </th>
                <th>Manager</th>
                <th>Distributor </th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalShop = 0;
                $totalVisit = 0;
                $serial = 1;
            @endphp
            @foreach ($data as $key => $row)
            @php
                // $total_order = DB::table('sale_orders')
                //     ->where('tso_id', $row->tso_id)
                //     ->where('shop_id', $row->id)
                //     ->when($from && $to, fn($q) => $q->whereBetween(DB::raw('DATE(sale_orders.created_at)'), [$from, $to]))
                //     ->groupBy('shop_id')
                //     ->count();
                // if ($total_order > 0) {
                //     continue;
                // }
                //     // dd($total_order, $row);

                // $unProductive = $row->total_shop - $total_order;

                $totalShop      += $row->total_shop;
                $totalVisit     += $row->total_visit;
            @endphp
                <tr>
                    <td>{{ $serial++ }}</td>
                    <td>{{ $row->shop_code }}</td>
                    <td>{{ $row->shop_name }}</td>
                    <td>{{ $row->total_visit }}</td>
                    <td>YES</td>
                    <td>{{ $row->route_name }}</td>
                    <td>{{ $row->tso }}</td>
                    <td>{{ $row->manager_name }}</td>
                    <td>{{ $row->distributor_name }}</td>
                </tr>
            @endforeach

            <tr class="fw-bold bg-light">
                <td>Total</td>
                <td></td>
                <td></td>
                
                {{-- <td>{{ $totalShop }}</td> --}}
                <td>{{ $totalVisit }}</td>
                <td></td>

                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
