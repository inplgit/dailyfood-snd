<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
$products = $master->get_all_product();
?>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr style="background-color: #f7f7f7;">
                <th>Date</th>
                @foreach ($products as $product)
                    <th>{{ $product->product_name }}</th>
                @endforeach
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandProductTotals = array_fill_keys($products->pluck('id')->toArray(), 0);
                $grandTotalQty = 0;
                $unitTotals = [];
            @endphp

            @foreach ($tsos as $tso)
                {{-- TSO Heading Row --}}
                <tr style="background-color: #d9edf7; font-weight: bold;">
                    <td colspan="{{ 2 + count($products) }}">{{ $tso->name }}</td>
                </tr>

                @foreach ($dates as $date)
                    @php
                        $rowQty = 0;
                        $unitSums = [];
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($date)->format('d-M-Y') }}</td>

                        @foreach ($products as $product)
                            @php
                                $qty = MasterFormsHelper::get_sale_qty_new(
                                    $date, $date,
                                    $product->id, 0, 0,
                                    $tso->id, $tso->distributor_id, 1
                                );

                                $rowQty += $qty;
                                $grandProductTotals[$product->id] += $qty;
                                $grandTotalQty += $qty;

                                if ($qty > 0 && $product->unit_name) {
                                    $unit = $product->unit_name;
                                    $unitSums[$unit] = ($unitSums[$unit] ?? 0) + $qty;
                                    $unitTotals[$unit] = ($unitTotals[$unit] ?? 0) + $qty;
                                }
                            @endphp
                            <td>{{ $qty > 0 ? number_format($qty) : '-' }}</td>
                        @endforeach

                        <td>
                            @if ($rowQty > 0)
                                {{ number_format($rowQty) }}
                                @if (!empty($unitSums))
                                    <br><small>
                                        ({{ collect($unitSums)->map(fn($val, $unit) => number_format($val) . ' ' . $unit)->implode(', ') }})
                                    </small>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endforeach

            {{-- Grand Totals Row --}}
            <tr style="background-color: #e9ecef; font-weight: bold;">
                <td>Grand Total</td>
                @foreach ($products as $product)
                    <td>{{ $grandProductTotals[$product->id] > 0 ? number_format($grandProductTotals[$product->id]) : '-' }}</td>
                @endforeach
                <td>
                    {{ number_format($grandTotalQty) }}
                    @if (!empty($unitTotals))
                        <br>
                        <small>
                            ({{ collect($unitTotals)->map(fn($sum, $unit) => number_format($sum) . ' ' . $unit)->implode(', ') }})
                        </small>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>
