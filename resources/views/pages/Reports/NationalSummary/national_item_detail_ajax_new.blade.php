<?php
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
$products = $master->get_all_product();
?>
<style>
.table-bordered > thead > tr > th,.table-bordered > tbody > tr > th,.table-bordered > tfoot > tr > th{vertical-align:inherit !important;text-align:left !important;padding:5px 5px !important;font-size:11px !important;}
.userlittab > thead > tr > td,.userlittab > tbody > tr > td,.userlittab > tfoot > tr > td{vertical-align:inherit !important;text-align:left !important;padding:5px 5px !important;font-size:11px !important;}
.dates-info-head p{text-align:center;margin-bottom:20px;color:#000 !important;}
strong{font-weight:bold !important;color:#000!important;}
</style>


<div class="table-responsive printBody">
    {{-- <div id="data1"> --}}
        @if(isset($from) && isset($to))
            @php
                $cityName = $city ? \App\Models\City::find($city)->name : 'All';
                $distributorName = $distributor_id ? \App\Models\Distributor::find($distributor_id)->distributor_name : 'All';
                $tsoName = $tso_id ? \App\Models\TSO::find($tso_id)->name : 'All';
            @endphp

            <div class="dates-info-head" >
                <p>
                    <strong>Laziza International</strong><br>
                    <strong>Brand / Area Wise Daily Sales Sheet</strong><br>
                    <b>From:</b> {{ \Carbon\Carbon::parse($from)->format('d-M-Y') }} |
                    <b>To:</b> {{ \Carbon\Carbon::parse($to)->format('d-M-Y') }} |
                    <b>City:</b> {{ $cityName }} |
                    <b>Distributor:</b> {{ $distributorName }} |
                    <b>TSO:</b> {{ $tsoName }}
                </p>
            </div>
        @endif
        <br>
        <table class="table sale_older_tab userlittab table table-bordered sf-table-list sale-list filterTable" >
            <thead>
                <tr style="background-color: #f7f7f7; !important;">
                    <th>Date</th>
                    @foreach ($products as $product)
                        <th>{{ $product->product_short_name ?? $product->product_name }}</th>
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
                    <tr style="background-color: #d9edf7 !important; font-weight: bold;!important">
                        <td colspan="{{ count($products) + 2 }}">{{ $tso->name }}</td>
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
                <tr style="background-color: #e9ecef!important; font-weight: bold;!important">
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
    {{-- </div> --}}
</div>
