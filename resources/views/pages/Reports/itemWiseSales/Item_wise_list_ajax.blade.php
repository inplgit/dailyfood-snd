
<?php
use App\Models\ShopVisit;
use App\Models\SaleOrder;
use App\Models\Route;
use App\Models\Shop;
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();
?>
<div class="table-responsive">
<table id="dataTable" class="table table-bordered">
    <thead>
  <tr class="text-center">
   <th>S.NO</th>
   <th>TSO</th>
   <th>Distributor Name</th>
   <th>CNIC</th>
   <th>Product</th>
   <th>Product Flavour</th>
   <th>Carton Size</th>
   <th>CIty </th>
   <!-- <th>QTY </th>
   <th>QTY in Ctn</th>
   <th>Amount </th> -->


     <th> Order qty</th>
                <th> Execute qty</th>
                <th> Return qty</th>
                <th> Balance qty</th>

    </tr>
</thead>
<tbody>
@php
 $total_qty = 0;
 $total_ctn_qty = 0;
 $total_val = 0;
   $sales_amount_reutn_total = 0;

   $total = 0;
                $total_exe = 0;
                $total_bal = 0;
 @endphp
    @foreach($data as $key => $row)
    @php



$sales_count = DB::table('sale_orders')
    ->join('sale_order_data', 'sale_order_data.so_id', 'sale_orders.id')
    ->whereBetween('sale_orders.dc_date', [$from, $to])
    ->where('sale_orders.tso_id', $row->tso_id)
    ->where('sale_order_data.product_id', $row->product_id)
    ->where('sale_orders.status', 1)
    ->where('sale_orders.distributor_id', $row->distributor_id)
    ->pluck('sale_orders.id'); 

$sales_amount = DB::table('sale_order_data')
    ->whereIn('so_id', $sales_count)
     ->where('sale_order_data.product_id', $row->product_id)
    ->sum('qty');




$sales_amount_exe = DB::table('sale_orders')
    ->join('sale_order_data', 'sale_order_data.so_id', 'sale_orders.id')
    ->whereIn('sale_order_data.so_id', $sales_count) 
    ->where('sale_orders.excecution', 1)
    ->where('sale_order_data.product_id', $row->product_id)
    ->sum('sale_order_data.qty');



                                      $sales_amount_reutn = DB::table('sales_return_data')
                                                            ->join('sale_order_data', 'sales_return_data.sales_order_data_id', 'sale_order_data.id')
                                                            ->whereIn('sale_order_data.so_id', $sales_count)
                                                            ->sum('sales_return_data.qty');

                                                       




                                          $sales_amount_reutn_total += $sales_amount_reutn = DB::table('sales_return_data')
                                                                        ->join('sale_order_data', 'sales_return_data.sales_order_data_id', 'sale_order_data.id')
                                                                        ->whereIn('sale_order_data.so_id', $sales_count)
                                                                        ->sum('sales_return_data.qty');


                                        $balance_amount = $sales_amount - $sales_amount_exe;
                                    $total += $sales_amount;
                                    $total_exe += $sales_amount_exe;
                                    $total_bal += $balance_amount;

                                     



        $product_data = $master->get_product_by_id($row->product_id);

        $ctn_qty = 0;
        // $ctn_qty = $row->qty / $product_data->carton_size;


        $get_qty = '';
        $product_price = '';
        foreach (MasterFormsHelper::get_product_price($row->product_id) as $k => $productPrice) {

            $qty = MasterFormsHelper::get_sale_qty2($from , $to , $row->product_id, $row->flavour_id , $productPrice->uom_id , $row->tso_id , $distributor_id ,$city);

            $uom_name = $master->uom_name($productPrice->uom_id); // Get UOM name for each product_price UOM
            if ($qty > 0) {
                $get_qty .= ($get_qty ? ' , ' : '') . number_format($qty) . 'x' . $uom_name;
                $grand_total_qty[$productPrice->uom_id] = isset($grand_total_qty[$productPrice->uom_id]) ? $grand_total_qty[$productPrice->uom_id]+$qty : $qty;
                if ($productPrice->uom_id != 7) {
                    $ctn_qty += $qty / ($productPrice->pcs_per_carton??1);
                }
                else {
                    $ctn_qty += $qty;
                }
            }

          

            $product_price .= ($product_price ? ' , ' : '') . number_format($productPrice->trade_price , 2) . '(' . $uom_name . ')';

        }

        $total_ctn_qty += $ctn_qty;
    @endphp
    <tr class="text-center">
        <td>{{  ++ $key }}</td>
        <td>{{ $row->name }}</td>
        <td>{{ $row->distributor_name }}</td>
        <td>{{ $row->cnic }}</td>
        <td>{{ $row->product_name }}</td>
        <td>{{ $master->get_flavour_name($row->flavour_id) }}</td>
        <td>1x{{ $product_data->carton_size ?? '--' }}</td>

        <td>{{ $row->city_name }}</td>
        <!-- <td>{{ $get_qty }}</td>
        {{-- <td>{{ $row->qty }}</td> --}}
        <td>{{ number_format($ctn_qty , 2)}}</td>
        <td>{{ number_format($row->total,2) }}</td> -->


                                <td>{{ number_format($sales_amount, 2) }}</td>
                                  <td>{{ (($sales_amount_exe ?? 0) - ($sales_amount_reutn ?? 0))  }}</td>
                                <td>{{ number_format($sales_amount_reutn, 2) }}</td>
                                <td>{{ number_format($balance_amount, 2) }}</td>

    </tr>
    @php
    $total_qty +=$row->qty;
    $total_val +=$row->total;
    @endphp
    @endforeach
    <tr style="background-color: lightgray;font-size: large;font-weight: bold" class="text-center">
        <td>Total</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <!-- <td>{{ number_format($total_qty,2) }}</td>
        <td>{{ number_format($total_ctn_qty,2) }}</td>
        <td>{{ number_format($total_val,2) }}</td> -->

        <td>{{ number_format($total, 2) }}</td>
                <td>
                {{ number_format($total_exe - $sales_amount_reutn_total, 2) }}</td>
                <td>{{number_format($sales_amount_reutn_total,2)}}</td>
                <td>{{ number_format($total_bal, 2) }}</td>
    </tr>

</tbody>
</table>
</div>
