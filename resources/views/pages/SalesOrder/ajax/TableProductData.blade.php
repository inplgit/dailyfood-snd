<tr>
    {{-- <td>
        <input class="form-control" type="text" name="" value="">
    </td> --}}
    <td>
        {{-- <input class="form-control" type="hidden" name="product_id[]" value="{{ $product->id }}">
        {{ $product->product_name }} --}}
        <select name="product_id[]" tabindex="-1" id="searchbox"
            required class="combobox form-control" aria-hidden="true">
            <option value="">Select a Product</option>
            @foreach ($products as $product)                                                                
                <option data-url="{{ route('sale-order.table-row', $product->id) }}" value="{{ $product->id }}">{{ $product->product_name }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <input class="form-control data-quantity" type="number" min="0" name="qty[]" value="0">
    </td>
    <td>
        <input class="form-control data-rate" type="number" min="0" name="rate[]" value="0">
    </td>
    <td>
        <input readonly class="form-control data-discount" type="text" name="discount[]" value="0">
        <input class="data-discount-amount" type="hidden" name="discount_amount[]" value="0">
    </td>
    <td>
        <input readonly class="form-control data-tax-percent" type="text" name="tax_percent[]" value="0">
        <input class="data-tax-amount" type="hidden" name="tax_amount[]" value="0">
    </td>
    <td>
        <input readonly class="form-control" type="text" name="t_offer[]" value="0">
        <input class="" type="hidden" name="t_offer[]" value="0">
    </td>
    <td>    
        <input readonly class="form-control data-total" type="text" name="total[]" value="0">
    </td>
    <td>
        
    </td>
</tr>