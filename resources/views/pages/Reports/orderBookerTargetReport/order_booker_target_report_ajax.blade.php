<?php
use App\Models\SaleOrder;
use Illuminate\Support\Facades\DB;
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
use App\Models\ProductPrice;

?>
<div class="table-responsive printBody">
    @if (isset($from) && isset($to))
        {{-- @php
            // $cityName = $city ? \App\Models\City::find($city)->name : 'All';
            $distributorName = $distributor_id
                ? \App\Models\Distributor::find($distributor_id)->distributor_name
                : 'All';
            $tsoName = $tso_id ? \App\Models\TSO::find($tso_id)->name : 'All';
        @endphp --}}
        @php
            // Distributor Names
            if (!empty($distributor_id)) {
                $distributorName = \App\Models\Distributor::whereIn('id', (array) $distributor_id)
                    ->pluck('distributor_name')
                    ->implode(', ');
            } else {
                $distributorName = 'All';
            }

            // TSO Names
            if (!empty($tso_id)) {
                $tsoName = \App\Models\TSO::whereIn('id', (array) $tso_id)->pluck('name')->implode(', ');
            } else {
                $tsoName = 'All';
            }
        @endphp

        <div class="dates-info-head text-center">
            <p>
                <strong>Laziza International</strong><br>
                <strong>Order Booker Target Sheet</strong><br>
                <b>From:</b> {{ \Carbon\Carbon::parse($from)->format('d-M-Y') }} |
                <b>To:</b> {{ \Carbon\Carbon::parse($to)->format('d-M-Y') }} |
                <b>Distributor:</b> {{ $distributorName }} |
                <b>TSO:</b> {{ $tsoName }}
            </p>

        </div>
    @endif
    <table class="table table-bordered filterTable">
        <thead>
            <th title="serial_number">S/no</th>
            {{-- <th title="distributor">Distributer</th> --}}
            {{-- <th title="tso">TSO</th> --}}

            @if ($target_type == 1)
                <th title="product_name">Product Name</th>
                <th title="qty">Quantity Target</th>
                <th title="target_value">Target Value</th>
            @elseif ($target_type == 2)
                <th title="target_value">Target Value</th>
            @elseif ($target_type == 3)
                <th title="shop_type">Shop Type</th>
                <th title="shop_target">Shop Target</th>
            @endif
            <th title="achieved_qty">Quantity Sold</th>
            <th title="achieved_amount">Net Sales</th>
            <th title="achievement_percentage">Progress %</th>
        </thead>

        <tbody>


            @php
                $i = 1;
                $total_target = 0;
                $total_value = 0;
                $total_achive = 0;
                $total_achive_amount = 0;
                $achievement_percentage_total = 0;
            @endphp

            @foreach ($tso_target as $target)
                @php
                    $dateStart = $from ?? now()->startOfMonth()->format('Y-m-d');
                    $dateEnd   = $to ?? now()->endOfMonth()->format('Y-m-d');
                    // $month = $monthfrom; // e.g. "09"
                    // $year = date('Y'); // current year ya request se le lo
                    $tsoIds = !empty($target->tso_ids) ? explode(',', $target->tso_ids) : [];

                    // $monthStart = date('Y-m-01', strtotime("$year-$month-01")); // e.g. 2025-09-01
                    // $monthEnd = date('Y-m-t', strtotime("$year-$month-01")); // e.g. 2025-09-30
                    $achived = DB::table('sale_orders')
                        ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
                        ->join('product_prices', function ($join) {
                            $join
                                ->on('product_prices.product_id', '=', 'sale_order_data.product_id')
                                ->where('product_prices.status', 1);
                        })
                        ->whereIn('sale_orders.tso_id', $tsoIds)
                        ->where('sale_order_data.product_id', $target->product_id)
                        ->whereBetween('sale_orders.dc_date', [$dateStart, $dateEnd])
                        // ->whereBetween('sale_orders.dc_date', [$monthStart, $monthEnd])
                        ->where('sale_orders.status', 1)
                        ->select(
                            DB::raw('SUM(sale_order_data.qty) as achieved_qty'),
                            DB::raw('SUM(sale_order_data.total) as achieved_amount'),
                        )
                        ->groupBy('sale_order_data.product_id')
                        ->first();

                    //dd($achived->toSql(), $achived->getBindings());

                    $achieved_qty = $achived->achieved_qty ?? 0;
                    $achieved_amount = $achived->achieved_amount ?? 0;

                    // Totals
                    if ($target_type == 1) {
                        $total_target += $target->tso_targets_qty ?? 0;
                        $total_value += ($target->target_value ?? 0);
                        // $total_target += $target->qty ?? 0;
                        // $total_value += ($target->trade_price ?? 0) * ($target->qty ?? 0);
                    } elseif ($target_type == 2) {
                        $total_target += $target->amount ?? 0;
                    } elseif ($target_type == 3) {
                        $total_target += $target->shop_qty ?? 0;
                    }

                    $total_achive += $achieved_qty;
                    $total_achive_amount += $achieved_amount;

                    $achievement_percentage = $target->tso_targets_qty > 0
                        ? round(($achieved_qty / $target->tso_targets_qty) * 100, 2)
                        : 0;
                    // $achievement_percentage =
                    //     ($target->qty ?? ($target->amount ?? $target->shop_qty)) > 0
                    //         ? round(($achieved_qty / ($target->qty ?? 1)) * 100, 2)
                    //         : 0;

                    // $achievement_percentage_total += $achievement_percentage;
                @endphp

                <tr>
                    <td>{{ $i++ }}</td>
                    {{-- <td>{{ $target->distributor_name }}</td> --}}
                    {{-- <td>{{ $target->tso_name }}</td> --}}

                    @if ($target_type == 1)
                        <td>{{ $target->product_name }}</td>
                        <td>{{ $target->tso_targets_qty ?? 0 }}</td>
                        <td>{{ number_format($target->target_value ?? 0, 2) }}</td>
                        {{-- <td>{{ $target->qty ?? 0 }}</td>
                        <td>{{ number_format(($target->trade_price ?? 0) * ($target->qty ?? 0), 2) }}</td> --}}
                    @elseif ($target_type == 2)
                        <td>{{ $target->amount ?? 0 }}</td>
                    @elseif ($target_type == 3)
                        <td>{{ $master->shop_type_name($target->shop_type) }}</td>
                        <td>{{ $target->shop_qty ?? 0 }}</td>
                    @endif

                    <td>{{ $achieved_qty }}</td>
                    <td>{{ number_format($achieved_amount, 2) }}</td>
                    <td>{{ $achievement_percentage }}%</td>
                </tr>
            @endforeach

            @php $achievement_percentage_total = $total_target > 0 ? round(($total_achive / $total_target) * 100, 2) : 0; @endphp

            <tr class="fw-bold bg-light">
                <td colspan="{{ $target_type == 1 ? 2 : ($target_type == 2 ? 1 : 1) }}">Total</td>

                @if ($target_type == 1)
                    <td>{{ number_format($total_target, 2) }}</td>
                    <td>{{ number_format($total_value, 2) }}</td>
                @elseif ($target_type == 2)
                    <td>{{ number_format($total_target, 2) }}</td>
                @elseif ($target_type == 3)
                    <td></td>
                    <td>{{ $total_target }}</td>
                @endif

                <td>{{ number_format($total_achive, 2) }}</td>
                <td>{{ number_format($total_achive_amount, 2) }}</td>
                <td>{{ $achievement_percentage_total }}%</td>
            </tr>
        </tbody>
    </table>
</div>
