<?php
use App\Models\Shop;
use App\Models\Route;
use App\Models\SaleOrder;
use App\Models\Product;
use App\Helpers\MasterFormsHelper;

$master = new MasterFormsHelper();
$user_allocate = $master->get_assign_user()->toArray();
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

    @if (request()->has('from') && request()->has('to'))
        @php
            $from = request()->get('from');
            $to = request()->get('to');
            $city = request()->get('city');
            $distributor_id = request()->get('distributor_id');
            $tso_id = request()->get('tso_id');

            $cityName = $city ? \App\Models\City::find($city)->name ?? 'N/A' : 'All';
            $distributorName = $distributor_id
                ? \App\Models\Distributor::find($distributor_id)->distributor_name ?? 'N/A'
                : 'All';
            $tsoName = $tso_id ? \App\Models\TSO::find($tso_id)->name ?? 'N/A' : 'All';
        @endphp

        <div class="dates-info-head">
            <p>
                <strong>Laziza International</strong><br>
                <strong>Brand Distribution Report</strong><br>
                <b>From:</b> {{ \Carbon\Carbon::parse($from)->format('d-M-Y') }} |
                <b>To:</b> {{ \Carbon\Carbon::parse($to)->format('d-M-Y') }} |
                <b>City:</b> {{ $cityName }} |
                <b>Distributor:</b> {{ $distributorName }} |
                <b>TSO:</b> {{ $tsoName }}
            </p>
        </div>
    @endif

    <table id="datatable" class="table table-bordered filterTable">
        <thead>
            <tr>
                <th>S.No</th>
                <th>TSO Name</th>
                <th>City</th>
                <th>Distributor</th>
                <th>Product</th>
                <th>Total Shops</th>
                {{-- <th>Shop Visits</th> --}}
                <th>Productive Shops</th>
                <th>Sale Units</th>
                <th>Drop Size</th>
            </tr>
        </thead>
        {{-- <tbody>
                @if (request()->has('from') && request()->has('to'))
                    @php
                        $from = request()->get('from');
                        $to = request()->get('to');
                        $city = request()->get('city');
                        $distributor_id = request()->get('distributor_id');
                        $tso_id = request()->get('tso_id');

                        $cityName = $city ? \App\Models\City::find($city)->name ?? 'N/A' : 'All';
                        $distributorName = $distributor_id ? \App\Models\Distributor::find($distributor_id)->distributor_name ?? 'N/A' : 'All';
                        $tsoName = $tso_id ? \App\Models\TSO::find($tso_id)->name ?? 'N/A' : 'All';
                    @endphp

                    <div class="dates-info-head">
                        <p>
                            <strong>Laziza International</strong><br>
                            <strong>Brand Distribution Report</strong><br>
                            <b>From:</b> {{ \Carbon\Carbon::parse($from)->format('d-M-Y') }} |
                            <b>To:</b> {{ \Carbon\Carbon::parse($to)->format('d-M-Y') }} |
                            <b>City:</b> {{ $cityName }} |
                            <b>Distributor:</b> {{ $distributorName }} |
                            <b>TSO:</b> {{ $tsoName }}
                        </p>
                    </div>
                @endif

                @php
                    $i = 1;
                    $total_productive = 0;
                    $total_sale_units = 0;
                @endphp

                @foreach ($tsos as $tso)
                    @if (in_array($tso['user_id'], $user_allocate))
                        @php
                            $routes = DB::table('route_tso')
                                ->where('tso_id', $tso['id'])
                                ->distinct()
                                ->pluck('route_id');

                            $shop_count = DB::table('shop_tso')
                                ->join('shops', 'shop_tso.shop_id', '=', 'shops.id')
                                ->where('shop_tso.tso_id', $tso['id'])
                                ->whereIn('shops.route_id', $routes)
                                ->when(!empty($shop_id), function ($query) use ($shop_id) {
                                    return $query->where('shops.id', $shop_id);
                                })
                                ->when(!empty($route_id), function ($query) use ($route_id) {
                                    return $query->where('shops.route_id', $route_id);
                                })
                                ->distinct('shop_tso.shop_id')
                                ->count('shop_tso.shop_id');

                            
                            $shop_visits = DB::table('shops as a')
                                ->join('shop_visits as sv', 'a.id', '=', 'sv.shop_id')
                                ->join('shop_tso as st', 'a.id', '=', 'st.shop_id')
                                ->where('st.tso_id', $tso['id'])
                                ->whereIn('a.route_id', $routes)
                                ->when(!empty($shop_id), function ($query) use ($shop_id) {
                                    return $query->where('a.id', $shop_id);
                                })
                                ->when(!empty($route_id), function ($query) use ($route_id) {
                                    return $query->where('a.route_id', $route_id);
                                })
                                ->when($from && $to, function ($query) use ($from, $to) {
                                    return $query->whereBetween('sv.visit_date', [$from, $to]);
                                })
                                ->count('sv.id');

                                $sales_orders = DB::table('sale_orders')
                                    ->whereBetween('dc_date', [$from, $to])
                                    ->where('tso_id', $tso['id'])
                                    ->where('distributor_id', $tso['distributor_id'])
                                    ->when(!empty($shop_id), function ($query) use ($shop_id) {
                                        return $query->where('sale_orders.shop_id', $shop_id);
                                    })
                                    ->where('status', 1)
                                    ->pluck('id');

                            $productive_count = $sales_orders->count();

                            $product_names = DB::table('sale_order_data')
                                ->whereIn('so_id', $sales_orders)
                                ->join('products', 'products.id', '=', 'sale_order_data.product_id')
                                ->select('products.product_name')
                                ->distinct()
                                ->pluck('products.product_name')
                                ->implode(', ');

                            $total_units = DB::table('sale_order_data')
                                ->whereIn('so_id', $sales_orders)
                                ->sum('qty');

                            $drop_size = $productive_count > 0 ? number_format($total_units / $productive_count, 2) : '0.00';

                            // Add to overall totals
                            $total_productive += $productive_count;
                            $total_sale_units += $total_units;
                        @endphp

                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $tso['name'] }}</td>
                            <td>{{ $tso['cities']['name'] ?? '--' }}</td>
                            <td>{{ $master->get_distributor_name($tso['distributor_id']) ?? '--' }}</td>
                            <td title="{{ $product_names }}">{{ \Illuminate\Support\Str::limit($product_names, 50) }}</td>
                            <td class="text-center">{{ $shop_count }}</td>
                            <td class="text-center">{{ $shop_visits }}</td>
                            <td class="text-center">{{ $productive_count }}</td>
                            <td class="text-center">{{ $total_units }}</td>
                            <td class="text-center">{{ $drop_size }}</td>
                        </tr>
                    @endif
                @endforeach

                <tr style="font-weight: bold; background-color: #f0f0f0;">
			        <td></td><td></td><td></td><td></td>
                    <td  class="text-right">Total</td>
                    <td></td>
                    <td></td>
                    <td class="text-center">{{ $total_productive }}</td>
                    <td class="text-center">{{ $total_sale_units }}</td>
                    <td class="text-center">
                        {{ $total_productive > 0 ? number_format($total_sale_units / $total_productive, 2) : '0.00' }}
                    </td>
                </tr>
            </tbody> --}}
        <tbody>
            @php $i = 1; @endphp
            @foreach ($reportData as $row)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $row['tso']->name }}</td>
                    <td>{{ $row['tso']->cities->name ?? '--' }}</td>
                    <td>{{ $row['tso']->distributor->distributor_name ?? '--' }}</td>
                    <td>{{ $row['product'] }}</td>
                    <td class="text-center">{{ $row['shop_count'] }}</td>
                    {{-- <td class="text-center">{{ $row['shop_visits'] }}</td> --}}
                    <td class="text-center">{{ $row['productive_count'] }}</td>
                    <td class="text-center">{{ $row['total_units'] }}</td>
                    <td class="text-center">{{ $row['drop_size'] }}</td>
                </tr>
            @endforeach

            {{-- ✅ Totals Row --}}
            <tr style="font-weight: bold; background-color: #f0f0f0;">
                <td colspan="6" class="text-right">Total</td>
                <td class="text-center">{{ $totals['productive'] }}</td>
                <td class="text-center">{{ $totals['sale_units'] }}</td>
                <td class="text-center">{{ $totals['drop_size'] }}</td>
            </tr>
        </tbody>


    </table>
</div>
