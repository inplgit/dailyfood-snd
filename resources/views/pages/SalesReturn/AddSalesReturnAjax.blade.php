@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
@endphp

@if(empty($so_data))
    <div class="alert alert-danger">No Data Found</div>
    @php return; @endphp
@endif

@if($so_data->excecution == 0)
    <div class="alert alert-warning">This order has not been executed yet.</div>
    @php return; @endphp
@endif


    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label> SO No</label>
                <input type="text" readonly name="so_no" value="{{ $so_data->invoice_no }}" class="form-control" placeholder="UOM Name"/>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label> Distributor</label>
                <input type="text" readonly value="{{ $so_data->distributor->distributor_name }}" name="uom_name" class="form-control" placeholder="UOM Name"/>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label> TSO</label>
                <input type="text" readonly value="{{ $so_data->tso->name }}" name="uom_name" class="form-control" placeholder="UOM Name"/>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label> Shop</label>
                <input type="text" readonly value="{{ $so_data->shop->company_name }}" name="uom_name" class="form-control" placeholder="UOM Name"/>
            </div>
        </div>

        <input type="hidden" name="so_id" value="{{ $so_data->id }}"/>
    </div>

    <table>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Sr No</th>
                <th>Product Name</th>
                <th>Product Flavour</th>
                <th>Sale Type</th>
                <th>QTY</th>
                <th>Previous Return</th>
                <th>Return </th>
            </tr>
            </thead>
            <tbody id="">

                @foreach ($so_data->saleOrderData()->get() as $key => $row )
                @php
                $return_qty=$master->getReturnQty($row->id);
                $qty = $row->qty;
                @endphp
                <tr class="text-center">
                <td>{{ ++$key }}</td>
                <td>{{ $row->product->product_name }}</td>
                <td>{{ $row->product_flavour->flavour_name }}</td>
                <td>{{$row->SaleTypeName}}</td>
                <td>{{ $row->qty }}</td>
                <td> {{ number_format($return_qty,2) }}</td>
                <td> <input id="return{{ $key }}" onkeyup="calc({{ $qty }},{{ $return_qty }},{{ $key }})" class="form-control" type="number" step="0.00" value="" name="qty[]"/></td>
                <input  type="hidden" name="so_data_id[]" value="{{ $row->id }}" />
                </tr>
                @endforeach
</tbody>
</table>
</br>
<div class="col-12">
    <button type="submit" class="btn btn-primary mr-1">Create Item</button>
    <button type="reset" class="btn btn-outline-secondary">Reset</button>
</div>






