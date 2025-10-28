@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
    $total = 0;
@endphp
@foreach ($stocks as $key => $stock)
    @php
        // $qty = MasterFormsHelper::qtyInStock($stock->product->id, Request::get('distributor_id'), 0);
        $product_data = $stock->product;
        $distributor_data = $stock->distributor;

        // discount
        $min_discount = $distributor_data->min_discount;
        $min_discount = $min_discount > 0 ? $min_discount : 0;

        // product price
        // $product_price = MasterFormsHelper::get_trade_price($stock->product->id  , $stock->uom_id);
        // value
        // $value = $qty * $product_price;

        // discount value
        // $discount_value = ($value / 100) * $min_discount;
        // dump($discount_value , $min_discount);
        // actual value

        // $value = $value - $discount_value;

        // dump($stock->toArray());
        $get_qty = '';
        $product_price = '';
        $amount = 0;
        foreach ($stock->product->product_price as $k => $productPrice) {
            $qty = MasterFormsHelper::get_Stock($stock->product->id, $stock->flavour_id , $productPrice->uom_id ,Request::get('distributor_id'));
            // if ($qty > 0) {
            //     $get_qty += $qty . 'x' . $master->uom_name($stock->uom_id);
            // }
            $uom_name = $master->uom_name($productPrice->uom_id); // Get UOM name for each product_price UOM
            if ($qty > 0) {
                $get_qty .= ($get_qty ? ' , ' : '') . $qty . 'x' . $uom_name;
                $value = $qty * $productPrice->trade_price;
                $amount += $value;
            }
            // dump($qty ,$stock->product->id, $stock->flavour_id , $productPrice->uom_id ,Request::get('distributor_id'));
            $product_price .= ($product_price ? ' , ' : '') . number_format($productPrice->trade_price , 2) . '(' . $uom_name . ')';

        }


        // discount value
        $discount_value = ($amount / 100) * $min_discount;
        // dump($min_discount , $discount_value);

        $net_amount = $amount - $discount_value;
    @endphp
    <tr>
        <td>{{ ++$key }}</td>
        <td>{{ $product_data->product_name }}</td>
        <td>{{ MasterFormsHelper::get_flavour_name($stock->flavour_id) ?? '--' }}</td>
        <td>{{ $get_qty }}</td>
        {{-- <td>{{ $product_data->sales_price }}</td> --}}
        <td>{{$product_price}}</td>
        <td>{{$amount}}</td>
        <td>{{ $discount_value }} ({{$min_discount}}%)</td>
        {{-- <td>{{ $stock->distributor->min_discount }}</td> --}}
        <td>{{ number_format($net_amount) }}</td>

    </tr>
    @php
        $total += $net_amount;
    @endphp
@endforeach
<tr class='bold'>
    <td class="text-center" colspan="7">Total</td>
    <td>{{ number_format($total, 2) }}</td>
</tr>
