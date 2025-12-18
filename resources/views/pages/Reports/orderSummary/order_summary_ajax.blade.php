<?php
use App\Models\ShopVisit;
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
                <th>Designation</th>
                <th>Distributor</th>
                <th>Route Name</th>
                <th>City</th>
                <th>Login Time</th>
                <th>Logout Time</th>
                <th>Today Shop</th>
                <th>New Shop</th>
                <th>Total Visit Shops</th>
                <th>Productive Shops</th>
                <th>Unproductive Shops</th>
                <th>Order Count</th>
                <th>Total Amount Sale</th>
                <th>Executed Orders</th>
                <th>Return Orders</th>
                <th>Balance Orders</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 1;
                $total_orders = 0;
                $total_exe = 0;
                $total_bal = 0;
                $total_productive = 0;
                $total_unproductive = 0;
                $total_today_shop = 0;
                $total_new_shop = 0;
                $total_visit_shop_total = 0;
                $sales_amount_total = 0;
                $sales_return_total = 0;

                $fromDate = $date;
                $toDate = $to;
                $uniqueDays = [];

                $period = new DatePeriod(
                    new DateTime($fromDate),
                    new DateInterval('P1D'),
                    (new DateTime($toDate))->modify('+1 day')
                );

                foreach ($period as $new_date) {
                    $uniqueDays[] = $new_date->format('l');
                }
                $uniqueDays = array_unique($uniqueDays);
            @endphp

            @foreach ($tsos as $tso)
                @if (in_array($tso['user_id'], $user_allocate) && !empty($tso['attendence']))
                    @foreach ($tso['attendence'] as $row)
                        @php
                            $date = \Carbon\Carbon::parse($row['created_at'])->format('Y-m-d');
                            $day = \Carbon\Carbon::parse($row['created_at'])->format('l');

                            // Day-wise routes
                            $dayRoutes = Route::status()
                                ->join('route_tso as rt', 'rt.route_id', '=', 'routes.id')
                                ->join('route_days as rd', 'rd.route_id', '=', 'routes.id')
                                ->where('rt.tso_id', $tso['id'])
                                ->where('routes.distributor_id', $tso['distributor_id'])
                                ->where('rd.day', $day)
                                ->pluck('routes.id');

                            $routeNames = Route::whereIn('id', $dayRoutes)->pluck('route_name')->implode(', ');

                            // Shops
                            $todayShop = DB::table('shops')
                                ->whereIn('route_id', $dayRoutes)
                                ->whereIn('id', function ($q) use ($tso, $fromDate, $toDate) {
                                    $q->select('shop_id')
                                      ->from('sale_orders')
                                      ->where('distributor_id', $tso['distributor_id'])
                                      ->where('tso_id', $tso['id'])
                                      ->whereBetween('dc_date', [$fromDate, $toDate]);
                                })
                                ->count();

                            $shop_create = UsersLocation::where('user_id', $tso['user_id'])
                                ->where('table_name', 'shops')
                                ->whereDate('created_at', $date)
                                ->count();

                            $total_visited = ShopVisit::where('user_id', $tso['user_id'])
                                ->whereDate('visit_date', $date)
                                ->count();

                            $in = $row['in'] ?? '';
                            $out = $row['out'] ?? '';

                            // Sale Orders
                            $sales_count = DB::table('sale_orders')
                                ->where('dc_date', $date)
                                ->where('tso_id', $tso['id'])
                                ->where('status', 1)
                                ->where('distributor_id', $tso['distributor_id']);

                            $order_count = $sales_count->count();
                            $sales_amount = $sales_count->sum('total_amount');

                            $executed_orders = DB::table('sale_orders')
                                ->whereIn('id', $sales_count->pluck('id'))
                                ->where('excecution', 1)
                                ->count();

                            $balance_orders = $order_count - $executed_orders;

                            // Sales Returns
                            $return_orders = DB::table('sales_return_data')
                                ->join('sale_order_data', 'sales_return_data.sales_order_data_id', '=', 'sale_order_data.id')
                                ->whereIn('sale_order_data.so_id', $sales_count->pluck('id'))
                                ->distinct('sale_order_data.so_id')
                                ->count('sale_order_data.so_id');

                            // Totals
                            $total_orders += $order_count;
                            $total_exe += $executed_orders;
                            $total_bal += $balance_orders;
                            $total_productive += $order_count;
                            $total_unproductive += $total_visited;
                            $total_today_shop += $todayShop;
                            $total_new_shop += $shop_create;
                            $total_visit_shop_total += ($total_visited + $order_count);
                            $sales_amount_total += $sales_amount;
                            $sales_return_total += $return_orders;
                        @endphp

                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $tso['tso_code'] }}</td>
                            <td>{{ $tso['name'] }}</td>
                            <td>{{ $tso['designation']['name'] ?? '' }}</td>
                            <td>{{ $master->get_distributor_name($tso['distributor_id']) ?? '' }}</td>
                            <td>{{ $routeNames ?? '-' }}</td>
                            <td>{{ $tso['cities']['name'] ?? '' }}</td>
                            <td>{{ $in ? date('d-m-Y h:i:s', strtotime($in)) : '' }}</td>
                            <td>{{ $out ? date('d-m-Y h:i:s', strtotime($out)) : '' }}</td>
                            <td>{{ $todayShop }}</td>
                            <td>{{ $shop_create }}</td>
                            <td>{{ $total_visited + $order_count }}</td>
                            <td>{{ $order_count }}</td>
                            <td>{{ $total_visited }}</td>
                            <td>{{ $order_count }}</td>
                            <td>{{ number_format($sales_amount, 0) }}</td>
                            <td>{{ $executed_orders }}</td>
                            <td>{{ $return_orders }}</td>
                            <td>{{ $balance_orders }}</td>
                        </tr>
                    @endforeach
                @endif
            @endforeach

            <tr style="background-color: darkgray;font-weight: bold">
                <td>Total</td>
                <td colspan="4"></td>
                <td colspan="2"></td>
                <td colspan="2"></td>
                <td>{{ $total_today_shop }}</td>
                <td>{{ $total_new_shop }}</td>
                <td>{{ $total_visit_shop_total }}</td>
                <td>{{ $total_productive }}</td>
                <td>{{ $total_unproductive }}</td>
                <td>{{ $total_orders }}</td>
                <td>{{ number_format($sales_amount_total, 0) }}</td>
                <td>{{ $total_exe - $sales_return_total }}</td>
                <td>{{ $sales_return_total }}</td>
                <td>{{ $total_bal }}</td>
            </tr>
        </tbody>
    </table>
</div>
