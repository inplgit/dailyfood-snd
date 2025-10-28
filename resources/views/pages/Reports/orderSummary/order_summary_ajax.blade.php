<?php
use App\Models\ShopVisit;
use App\Models\SaleOrder;
use App\Models\Route;
use App\Models\Shop;
use App\Models\UsersLocation;
use App\Helpers\MasterFormsHelper;

$master = new MasterFormsHelper();
$user_allocate = $master->get_assign_user()->toArray();
?>

<div class="table-responsive printBody">
    @if (isset($from) && isset($to))
        @php
            // $cityName = $city ? \App\Models\City::find($city)->name : 'All';
            $distributorName = $distributor_id
                ? \App\Models\Distributor::find($distributor_id)->distributor_name
                : 'All';
            $tsoName = $tso_id ? \App\Models\TSO::find($tso_id)->name : 'All';
        @endphp
        <div class="dates-info-head text-center">
            <p>
                <strong>Laziza International</strong><br>
                <strong>TSO Summary</strong><br>
                <b>From:</b> {{ $from }} |
                <b>To:</b> {{ $to }} |
                <b>Distributor:</b> {{ $distributorName }} |
                <b>TSO:</b> {{ $tsoName }}
            </p>

        </div>
    @endif
    <table class="table table-bordered filterTable">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Emp Code</th>
                <th>Emp Name</th>
                <th>CNIC</th>
                <th>Designation</th>
                <th>Distributor</th>
                <th>City</th>
                <th>Login Time</th>
                <th>Log out Time</th>
                <th>Total Shops</th>
                <th>Today Shop</th>
                <th>Visited Shops</th>
                <th>Productive Shops</th>
                <th>Unproductive Shops</th>
                <th>Order qty</th>
                <th>Execute qty</th>
                <th>Return qty</th>
                <th>Balance qty</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 1;
                $total = 0;
                $total_exe = 0;
                $total_bal = 0;
                $total_productive = 0;
                $total_unproductive = 0;
                $total_shop = 0;
                $total_today_shop = 0;
                $total_visit_shop = 0;
                $total_visit_shop_total = 0;
                $sales_amount_reutn_total = 0;

                $fromDate = $date;
                $toDate = $to;
                $uniqueDays = [];

                $period = new DatePeriod(
                    new DateTime($fromDate),
                    new DateInterval('P1D'),  // Interval of 1 day
                    (new DateTime($toDate))->modify('+1 day')  // Inclusive of end date
                );

                foreach ($period as $new_date) {
                    $dayName = $new_date->format('l');  // Get day name
                    if (!in_array($dayName, $uniqueDays)) {
                        $uniqueDays[] = $dayName;  // Add only unique day names
                    }
                }
            @endphp

            @foreach ($tsos as $tso)
                @if (in_array($tso['user_id'], $user_allocate))
                    @if (!empty($tso['attendence']))
                        @foreach ($tso['attendence'] as $row)
                            @php
                                $date = Carbon\Carbon::parse($row['created_at'])->format('Y-m-d');
                                $timestamp = strtotime($date);
                                $day = date('l', $timestamp);

                                $route = Route::status()
                                    ->join('route_tso as rt', 'rt.route_id', '=', 'routes.id')
                                    ->where('rt.tso_id', $tso['id'])
                                    ->where('routes.distributor_id', $tso['distributor_id'])
                                    ->pluck('routes.id');

                                $date_in = date('Y-m-d', strtotime($row['in']));

                                $shop_count = Shop::status()
                                    ->join('shop_tso as st', function ($join) use ($tso) {
                                        $join->on('st.shop_id', '=', 'shops.id')->where('st.tso_id', $tso['id']);
                                    })
                                    ->where('shops.distributor_id', $tso['distributor_id'])
                                    ->whereIn('shops.route_id', $route)
                                    ->distinct('shops.id')
                                    ->count('shops.id');

                                $total_shop += $shop_count;

                                // $uniqueShops = \DB::table('sale_orders')
                                //     ->where('distributor_id', $tso['distributor_id'])
                                //     ->where('tso_id', $tso['id'])
                                //     ->whereBetween('dc_date', [$fromDate, $toDate])
                                //     ->distinct()
                                //     ->pluck('shop_id');
                                // $uniqueRoutes = \DB::table('shops')
                                //     ->whereIn('id', $uniqueShops)
                                //     ->distinct()
                                //     ->pluck('route_id');
                                // $todayShop = \DB::table('shops')
                                //     ->whereIn('route_id', $uniqueRoutes)
                                //     ->count();

                                $todayShop = \DB::table('shops')
                                    ->whereIn('route_id', function ($q) use ($tso, $fromDate, $toDate) {
                                        $q->select('route_id')
                                            ->from('shops')
                                            ->whereIn('id', function ($q2) use ($tso, $fromDate, $toDate) {
                                                $q2->select('shop_id')
                                                    ->from('sale_orders')
                                                    ->where('distributor_id', $tso['distributor_id'])
                                                    ->where('tso_id', $tso['id'])
                                                    ->whereBetween('dc_date', [$fromDate, $toDate]);
                                            })
                                            ->distinct();
                                    })
                                    ->count();

                                $total_today_shop += $todayShop;

                                $shop_create = UsersLocation::where('user_id', $tso['user_id'])
                                    ->where('table_name', 'shops')
                                    ->whereDate('created_at', $date)
                                    ->count();

                                // ✅ FIXED: remove ->groupBy('user_id')->count('id')
                                $total_visited = ShopVisit::where('user_id', $tso['user_id'])
                                    ->whereDate('visit_date', $date)
                                    ->count();

                                $in = $row['in'] ?? '';
                                $out = $row['out'] ?? '';
                            @endphp

                            <tr>
                                <td title="{{ $row['id'] }}">{{ $i++ }}</td>
                                <td>{{ $tso['tso_code'] }}</td>
                                <td>{{ $tso['name'] }}</td>
                                <td>{{ $tso['cnic'] ?? '--' }}</td>
                                <td>{{ $tso['designation']['name'] ?? '' }}</td>
                                <td>{{ $master->get_distributor_name($tso['distributor_id']) ?? '' }}</td>
                                <td>{{ $tso['cities']['name'] ?? '' }}</td>
                                <td>@if ($in) {{ date('d-m-Y h:i:s', strtotime($in)) }} @endif</td>
                                <td>@if ($out) {{ date('d-m-Y h:i:s', strtotime($out)) }} @endif</td>
                                <td>{{ $shop_count }}</td>
                                <td>{{ $todayShop ?? 0 }}</td>

                                @php
                                    $sales_count = DB::table('sale_orders')
                                        ->where('dc_date', $date)
                                        ->where('tso_id', $tso['id'])
                                        ->where('sale_orders.status', 1)
                                        ->where('distributor_id', $tso['distributor_id']);

                                    $sales_amount = DB::table('sale_order_data')
                                        ->whereIn('so_id', $sales_count->pluck('id'))
                                        ->sum('qty');

                                    $sales_amount_exe = DB::table('sale_orders')
                                        ->join('sale_order_data', 'sale_order_data.so_id', 'sale_orders.id')
                                        ->whereIn('sale_order_data.so_id', $sales_count->pluck('id'))
                                        ->where('excecution', 1)
                                        ->sum('sale_order_data.qty');

                                    $balance_amount = $sales_amount - $sales_amount_exe;
                                    $total += $sales_amount;
                                    $total_exe += $sales_amount_exe;
                                    $total_bal += $balance_amount;

                                    $productive_count = $sales_count->count() ?? 0;
                                    $total_productive += $productive_count;
                                    $total_unproductive += $total_visited;

                                    $sales_amount_reutn = DB::table('sales_return_data')
                                        ->join('sale_order_data', 'sales_return_data.sales_order_data_id', 'sale_order_data.id')
                                        ->whereIn('sale_order_data.so_id', $sales_count->pluck('id'))
                                        ->sum('sales_return_data.qty');

                                    $sales_amount_reutn_total += $sales_amount_reutn;
                                @endphp

                                <td>{{ $total_visited + $productive_count + $shop_create }}</td>
                                @php
                                    $total_visit_shop_total += $total_visited + $productive_count + $shop_create;
                                    $total_visit_shop += $total_visited + $productive_count + $shop_create;
                                @endphp
                                <td>{{ $productive_count }}</td>
                                <td>{{ $total_visited }}</td>
                                <td>{{ number_format($sales_amount, 2) }}</td>
                                <td>{{ $sales_amount_exe }}</td>
                                <td>{{ number_format($sales_amount_reutn, 2) }}</td>
                                <td>{{ number_format($balance_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endif
            @endforeach

            <tr style="background-color: darkgray;font-weight: bold">
                <td>Total</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $total_shop }}</td>
                <td>{{ $total_today_shop }}</td>
                <td>{{ $total_visit_shop_total }}</td>
                <td>{{ $total_productive }}</td>
                <td>{{ $total_unproductive }}</td>
                <td>{{ number_format($total, 2) }}</td>
                <td>{{ number_format($total_exe - $sales_amount_reutn_total, 2) }}</td>
                <td>{{ number_format($sales_amount_reutn_total, 2) }}</td>
                <td>{{ number_format($total_bal, 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>
