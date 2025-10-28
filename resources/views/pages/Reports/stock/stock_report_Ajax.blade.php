@php
    use App\Helpers\MasterFormsHelper;
    use App\Models\Attendence;

  $master=new MasterFormsHelper();
@endphp
<style>
    .table-container {
        height: 400px;
        /* Adjust the height as needed */
        overflow: auto;
    }

    /* Fix the table heading */
    #table_data thead {
        position: sticky;
        top: 0;
        background-color: white;
        /* Set to match your table's styling */
        z-index: 1;
    }
</style>
<div class="table-responsive table-container">
    <table id="table_data" class="table table-bordered">
        <thead>
            <thead>
                <tr class="text-center">
                    <th colspan="4"></th>
                    <th style="background-color: rgb(147 190 147)" colspan="2">Opening</th>
                    <th style="background-color: #c0c0d7" colspan="2">Purchase</th>
                    <th style="background-color: #edc7a3" colspan="2">Market Return</th>
                    <th style="background-color: #9999dd" colspan="2">Transfer Received</th>
                    <th style="background-color: #c0c0d7" colspan="2" class="hide">Un packing In</th>
                    <th style="background-color: #8080803b" colspan="2">Available Sales For Sales</th>
                    <th style="background-color: #edc7a3" colspan="2">Stock Transfer</th>
                    <th style="background-color: #9999dd" colspan="2">Sales (Execution Only) </th>
                    <th style="background-color: #c0c0d7" colspan="2" class="hide">Un packing OUT</th>
                    <th style="background-color: #12c52a" colspan="2">Closing </th>
                </tr>
            </thead>
            <tr>
                <th>S.No</th>
                <th>Prouct Name</th>
                <th>Flavour Name</th>
                <th>Distributor Name</th>
                {{-- opening --}}
                <th>QTY </th>
                <th>Value </th>

                {{-- Purchase --}}
                <th>QTY </th>
                <th>Value </th>

                {{-- Transfer Recived --}}
                <th>QTY </th>
                <th>Value </th>

                {{-- Return --}}
                <th>QTY </th>
                <th>Value </th>

                {{-- Un packing IN --}}
                <th class="hide">QTY </th>
                <th class="hide">Value </th>


                {{-- Avaiable --}}
                <th>QTY </th>
                <th>Value </th>

                {{-- stock transfer --}}
                <th>QTY </th>
                <th>Value </th>

                {{-- Sales --}}
                <th>QTY </th>
                <th>Value </th>

                {{-- Un packing OUT --}}
                <th class="hide">QTY </th>
                <th class="hide">Value </th>


                {{-- Closing --}}


                <th>QTY </th>
                <th>Value </th>


            </tr>
        </thead>
        <tbody>
            @php
                $i = 1;
            @endphp
            @foreach ($result as $item)
                @php


                    // get packing from products
                    $packing = $item->packing_size > 0 ? $item->packing_size : 1;

                    // get discount percentage from distributor

                    $discount_amount = ($item->sales_price / 100) * $item->max_discount;
                    $price = $item->sales_price - $discount_amount;


                    $get_opening = $master->get_stock_type_wise_data(
                       $item->product_id,
                      $item->flavour_id,
                     $item->distributor_id,
                    [1],
                   );


                    $opening_qty = $get_opening['main_qty'];
                    $open_amount = $get_opening['main_amount'];

                    $get_purchase = $master->get_stock_type_wise_data(
                        $item->product_id,
                        $item->flavour_id,
                        $item->distributor_id,
                        [0],
                    );
                    $purchase_qty = $get_purchase['main_qty'];
                    $purchase_amount = $get_purchase['main_amount'];

                    $get_sales_return = $master->get_stock_type_wise_data(
                        $item->product_id,
                        $item->flavour_id,
                        $item->distributor_id,
                        [4],
                    );
                    $sales_return_qty = $get_sales_return['main_qty'];
                    $return_amount = $get_sales_return['main_amount'];

                    $get_transfer_received = $master->get_stock_type_wise_data(
                        $item->product_id,
                        $item->flavour_id,
                        $item->distributor_id,
                        [2],
                    );
                    $transfer_received_qty = $get_transfer_received['main_qty'];
                    $transfer_received_amount = $get_transfer_received['main_amount'];



                    $get_unpacking_in = $master->get_stock_type_wise_data(
                        $item->product_id,
                        $item->flavour_id,
                        $item->distributor_id,
                        [6],
                    );
                    $unpacking_in_qty = $get_unpacking_in['main_qty'];
                    $unpacking_in_amount = $get_unpacking_in['main_amount'];




                    $get_available = $master->get_stock_type_wise_data(
                        $item->product_id,
                        $item->flavour_id,
                        $item->distributor_id,
                        [0, 1, 2, 4],
                    );
                    $available_qty = $get_available['main_qty'];
                    $available_amount = $get_available['main_amount'];

                    $get_transfer = $master->get_stock_type_wise_data(
                        $item->product_id,
                        $item->flavour_id,
                        $item->distributor_id,
                        [5],
                    );
                    $transfer_qty = $get_transfer['main_qty'];
                    $transfer_amount = $get_transfer['main_amount'];




                    $get_sales = $master->get_stock_type_wise_data(
                        $item->product_id,
                        $item->flavour_id,
                        $item->distributor_id,
                        [3],
                    );
                    $sales_qty = $get_sales['main_qty'];
                    $sales_amount = $get_sales['main_amount'];


                    $get_unpacking_out = $master->get_stock_type_wise_data(
                        $item->product_id,
                        $item->flavour_id,
                        $item->distributor_id,
                        [7],
                    );
                    $unpacking_out_qty = $get_unpacking_out['main_qty'];
                    $unpacking_out_amount = $get_unpacking_out['main_amount'];


                 

                    $closing_amount = 0;
                    $carton_qty = [];

                    foreach ($master->get_product_price($item->product_id) as $k => $productPrice) {
                        $qty = $master->get_Stock(
                            $item->product_id,
                            $item->flavour_id,
                            $productPrice->uom_id,
                            $item->distributor_id,
                        );

                        if ($qty > 0) {

                            $value = $qty * $productPrice->trade_price;
                            $closing_amount += $value;

                           if ($productPrice->pcs_per_carton > 0 && $qty >= $productPrice->pcs_per_carton && $productPrice->uom_id != 7) {

    $cartons = floor($qty / $productPrice->pcs_per_carton);
    $carton_qty[7] = isset($carton_qty[7]) ? $carton_qty[7] + $cartons : $cartons;

    // Calculate the remaining pieces
    $qty = $qty % $productPrice->pcs_per_carton;
}

                            $carton_qty[$productPrice->uom_id] = isset($carton_qty[$productPrice->uom_id]) ? $carton_qty[$productPrice->uom_id]+$qty : $qty;



                        }
                    }

                    $closing_qty = '';
                    foreach ($carton_qty as $uom_id => $qty) {
                        $uom_name = $master->uom_name($uom_id); // Get UOM name for each product_price UOM

                        $closing_qty .= ($closing_qty ? ' , ' : '') . number_format($qty) . 'x' . $uom_name;
                    }
                    // dump($carton_qty);
                    // dump($opening_qty , $item->product_id, $item->flavour_id , $item->distributor_id);
                @endphp
                <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $master->get_flavour_name($item->flavour_id) }}</td>
                    <td>{{ $master->get_distributor_name($item->distributor_id) }}</td>

                    {{-- opening --}}


                    <td>{{ $opening_qty ?? '--' }}</td>
                    <td>{{ number_format($open_amount, 2) }}</td>

                    {{-- Purchase --}}

                    <td>{{ $purchase_qty ?? '--' }}</td>
                    <td>{{ number_format($purchase_amount) }}</td>

                    {{-- Return --}}

                    <td>{{ $sales_return_qty ?? '--' }}</td>
                    <td>{{ number_format($return_amount, 2) }}</td>

                    {{-- Transfer Recived --}}

                    <td>{{ $transfer_received_qty ?? '--' }}</td>
                    <td>{{ number_format($transfer_received_amount, 2) }}</td>


                    {{-- Un packing IN  --}}
                    <td class="hide">{{ $unpacking_in_qty }}</td>
                    <td class="hide">{{ number_format($unpacking_in_amount, 2) }}</td>



                    {{-- Available --}}

                    <td>{{ $available_qty ?? '--' }}</td>
                    <td>{{ number_format($available_amount, 2) }}</td>


                    {{-- Transfer --}}

                    <td>{{ $transfer_qty ?? '--' }}</td>
                    <td>{{ $transfer_amount }}</td>


                    {{-- Sales --}}

                    <td>{{ $sales_qty ?? '--' }}</td>
                    <td>{{ number_format($sales_amount, 2) }}</td>


                    {{-- Un packing OUT  --}}

                    <td class="hide">{{ $unpacking_out_qty }}</td>
                    <td class="hide"> {{ number_format($unpacking_out_amount, 2) }}</td>



                    <td>{{ $closing_qty ?? '--' }}</td>
                    <td>{{ number_format($closing_amount, 2) }}</td>

                </tr>
                @php
                    $i++;
                @endphp
            @endforeach

        </tbody>
    </table>
</div>
