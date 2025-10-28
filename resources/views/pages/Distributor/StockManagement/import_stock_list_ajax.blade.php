@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
    $total = 0;
@endphp
@foreach ($stocks as $key => $stock)
    @php
        $qty = $stock->qty;
        $product_data = $stock->product;
        $distributor_data = $stock->distributor;

        // discount
        $min_discount = $distributor_data->min_discount;
        $min_discount = $min_discount > 0 ? $min_discount : 1;

        // product_price
        $product_price = MasterFormsHelper::get_trade_price($stock->product->id  , $stock->uom_id);

        // value
        $value = $qty * $product_price;

        // discount value
        $discount_value = ($value / 100) * $min_discount;

        // actual value

        $value = $value - $discount_value;

        // dump($stock->toArray());
    @endphp
    <tr>
        <td>{{ ++$key }}</td>
        <td>{{ $stock->voucher_date }}</td>
        <td>{{ $product_data->product_name }}</td>
        <td>{{ MasterFormsHelper::get_flavour_name($stock->flavour_id) }}</td>
        <td>{{ $master->uom_name($stock->uom_id) }}</td>
        <td>{{ $qty }}</td>
        {{-- <td></td> --}}
        {{-- <td>{{ $product_data->sales_price }}</td> --}}
        <td>{{$product_price}}</td>
        {{-- <td>{{ $stock->distributor->min_discount }}</td> --}}
        <td>{{ number_format($value) }}</td>

    </tr>
    @php
        $total += $value;
    @endphp
@endforeach
<tr class='bold'>
    <td class="text-center" colspan="7">Total</td>
    <td>{{ number_format($total, 2) }}</td>
</tr>
