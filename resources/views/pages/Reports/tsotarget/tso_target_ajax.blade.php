<?php
use App\Models\SaleOrder;
use Illuminate\Support\Facades\DB;
use App\Helpers\MasterFormsHelper;
use App\Models\ProductPrice;

$master = new MasterFormsHelper();
?>

<table class="table table-bordered" id="print_data">
    <thead>
        <th>S/no</th>
        <!-- <th>YT</th> -->
        <th>Distributer</th>
        <th>TSO</th>
       
        @if ($target_type == 1)
            <th>Product Name</th>
            <th>Product Flavor</th>
            <th>Target Qty</th>
           
            <th>Target Amount</th>
        @elseif ($target_type == 2)
            <th>Target Amount</th>
        @elseif ($target_type == 3)
            <th>Shop Type</th>
            <th>Shop Target</th>
        @endif
        @if ($summary == '1')
        <th>Booking Qty</th>
          
            <th>Booking  Amount</th>
            <th>Achieved Target</th>
           
            <th>Achieved Amount</th>
          
            <th>Achievement %</th>
        @endif
      
    </thead>
    <tbody>
        @php
            $i = 1;
            $total_target = $total_target_ctn = 0;
            $total_achive = $total_achived_ctn = $total_achive_amount = 0;
            $total_achive_unexcute = $total_achived_ctn_unexcute = $total_achive_unexcute_amount = 0;
            $total_trade_price_sum = 0;
            $achievement_percentage_total = 0;
        @endphp
        @foreach ($tso_target as $target)
            @php
                $pcsPerCarton = ProductPrice::where('product_id', $target->product_id)
                    ->where('uom_id', '!=', 7)
                    ->where('status', 1)
                    ->orderBy('start_date','desc')
                    ->value('pcs_per_carton') ?? 1;
                        $month = $monthfrom; // e.g. "09"
$year = date('Y');   // current year ya request se le lo

$monthStart = date("Y-m-01", strtotime("$year-$month-01")); // e.g. 2025-09-01
$monthEnd   = date("Y-m-t", strtotime("$year-$month-01"));  // e.g. 2025-09-30

$achived = DB::table('sale_orders')
    ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
    ->join('product_prices', function($join) {
        $join->on('product_prices.product_id', '=', 'sale_order_data.product_id')
             ->where('product_prices.status', 1);
    })
    ->where('sale_orders.tso_id', $target->tso_id)
    ->where('sale_order_data.product_id', $target->product_id)
    ->whereBetween('sale_orders.dc_date', [$monthStart, $monthEnd])
    ->where('sale_orders.status', 1)
    ->select(
        DB::raw('SUM(sale_order_data.qty) as achieved_qty'),
        DB::raw('SUM(sale_order_data.total) as achieved_amount')
    )
    ->groupBy('sale_order_data.product_id')
    ->first();
$achived_unexcute = DB::table('sale_orders')
    ->join('sale_order_data', 'sale_order_data.so_id', '=', 'sale_orders.id')
    ->where('sale_orders.tso_id', $target->tso_id)
    ->where('sale_orders.excecution', 0)
    ->where('sale_order_data.flavour_id', $target->flavour_id)
    ->where('sale_order_data.product_id', $target->product_id)
    ->whereBetween(DB::raw('MONTH(sale_orders.created_at)'), [(int)$monthfrom, (int)$monthto]) 
    ->select(
        DB::raw('COALESCE(SUM(sale_order_data.qty), 0) as achieved_qty'),
        DB::raw('COALESCE(SUM(sale_order_data.total), 0) as achieved_amount')
    )->first();



                $total_target += $target->type == 1 ? ($target->qty ?? 0) : ($target->type == 3 ? ($target->shop_qty ?? 0) : ($target->amount ?? 0));
                $total_target_ctn += $target->type == 1 ? (($target->qty ?? 0) / $pcsPerCarton) : 0;
                $total_achive += $target->type == 2 && $achived ? ($achived->achieved_amount ?? 0) : ($achived->achieved_qty ?? 0);
                $total_achived_ctn += ($achived->achieved_qty ?? 0) / ($pcsPerCarton ?: 1);
                $total_achive_amount += $achived->achieved_amount ?? 0;
                $total_achive_unexcute_single = $achived_unexcute->achieved_qty ?? 0;
                $total_achive_unexcute += $achived_unexcute->achieved_qty ?? 0;
                $total_achived_ctn_unexcute += ($achived_unexcute->achieved_qty ?? 0) / ($pcsPerCarton ?: 1);
                $total_achive_unexcute_amount += $achived_unexcute->achieved_amount ?? 0;

                $achievement_percentage = ($target->qty > 0) ? round((($achived->achieved_qty ?? 0) / $target->qty) * 100, 2) : 0;

              $achievement_percentage_total = ($total_target > 0) ? round(($total_achive / $total_target) * 100, 2) : 0;


                if ($target_type == 1 && $target->type == 1) {
                  
                    $total_trade_price_sum += ($target->trade_price ?? 0) * ($target->qty ?? 0);
                }
            @endphp
            <tr>
                <td>{{ $i++ }}</td>
                <!-- <td> {{$target->flavour_id}} - {{$target->product_id}} - {{$monthfrom}} -{{$target->tso_id}}</td> -->
                <td>{{ $target->distributor_name }}</td>
                <td>{{ $target->tso_name }}</td>
             
                @if ($target_type == 1)
                    <td>{{ $target->product_name }}</td>
                    <!-- <td>{{ $target->flavour_name }}</td> -->
                    <td>{{ $master->get_flavour_name($target->flavour_id) }}</td>
                    <td>{{ $target->qty ?? '' }}</td>
                  
                 
                    <td>{{ number_format(($target->trade_price ?? 0) * ($target->qty ?? 0)?? 0, 2) }}</td>
                @elseif ($target_type == 2)
                    <td>{{ $target->amount ?? '' }}</td>
                @elseif ($target_type == 3)
                    <td>{{ $master->shop_type_name($target->shop_type) }}</td>
                    <td>{{ $target->shop_qty ?? '' }}</td>
                @endif
                @if ($summary == '1')
                <td>{{ $achived_unexcute->achieved_qty  }}</td>
                   
                    <td>{{ number_format($achived_unexcute->achieved_amount, 2) }}</td>
                  <td>{{ optional($achived)->achieved_qty ?? 0 }}</td>
                  
                    <
                    <td>{{ number_format(optional($achived)->achieved_amount ?? 0, 2) }}</td>
                   
                    <td>{{ $achievement_percentage }}%</td>
                @endif
               
            </tr>
        @endforeach
        <tr>
            <td colspan="{{ $target_type == 2 ? 3 : 4 }}">Total</td>
            <td></td>
            <td>{{ $total_target }}</td>
          
            <td>{{ number_format($total_trade_price_sum, 2) }}</td>
            @if ($summary == '1')
            <td>{{ $total_achive_unexcute }}</td>
              
                <td>{{ number_format($total_achive_unexcute_amount, 2) }}</td>
                <td>{{ $total_achive }}</td>
            
                <td>{{ number_format($total_achive_amount, 2) }}</td>
              
                <td>{{ $achievement_percentage_total }}%</td>
            @endif
            
        </tr>
    </tbody>
</table>
