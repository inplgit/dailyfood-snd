@php
use App\Models\Product;
$product_data = new Product();
$uom_data = $product_data->get_all_uom();
use App\Helpers\MasterFormsHelper;
$master = new MasterFormsHelper();

@endphp
@extends('layouts.master')
@section('title', "SND || Caregory")
@section('content')


<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">ADD NEW PRODUCT</h4>
                </div>
                <div class="card-body">
                    <form id="subm" method="POST" action="{{ route('product.update',$product->id) }}" class="form">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Product Code <strong>*</strong></label>
                                    <input readonly type="text" value="{{ $product->product_code }}" class="form-control" placeholder="Product Code"/>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Product Name <strong>*</strong></label>
                                    <input name="product_name" type="text" value="{{ $product->product_name }}" class="form-control" placeholder="Product Name"/>
                                </div>
                            </div>


                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Product Category <strong>*</strong></label>
                                    <select name="category_id" class="select2 form-control form-control-lg">
                                        <option value="">Select One</option>

                                        @foreach ( $product_data->get_all_category() as $key => $row )
                                        <option @if($product->category_id==$row->id) selected @endif value="{{ $row->id }}">{{ $row->name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Product Type <strong>*</strong></label>
                                    <select name="product_type_id" class="select2 form-control form-control-lg">
                                        <option value="">Select</option>

                                        @foreach ($product_data->get_all_product_type() as $key => $row )
                                        <option @if($product->product_type_id==$row->id) selected @endif value="{{ $row->id }}">{{ $row->type_name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Brand <strong>*</strong></label>

                                    <select name="brand_id" class="select2 form-control form-control-lg">
                                        <option value="">Select</option>
                                        @foreach ($product_data->get_all_brand() as $key => $row )
                                        <option @if($product->brand_id==$row->id) selected @endif value="{{ $row->id }}">{{ $row->brand_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>



                            {{-- <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Product Unit <strong>*</strong></label>
                                    <select name="uom_id" class="select2 form-control form-control-lg">
                                        <option value="">Select</option>
                                        @foreach ($uom_data as $key => $row )
                                        <option @if($product->uom_id==$row->id) selected @endif value="{{ $row->id }}">{{ $row->uom_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Packing Unit <strong>*</strong></label>
                                    <select name="packing_uom_id" class="select2 form-control form-control-lg">
                                        <option value="">Select</option>
                                        @foreach ($uom_data as $key => $row )
                                        <option @if($product->packing_uom_id==$row->id) selected @endif value="{{ $row->id }}">{{ $row->uom_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}


                            <div class="col-md-3 col-12 mb-1">
                                <div class="form-group">
                                    <label>Locality</label>
                                    <select name="locality" class="select2 form-control form-control-lg">
                                        <option @if($product->locality==0) selected @endif value="0">Local</option>
                                        <option @if($product->locality==1) selected @endif value="1">Imported</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>SKU <strong>*</strong></label>
                                    <input value="{{ $product->SKU }}" name="SKU" type="text" class="form-control" placeholder="SKU" />
                                </div>
                            </div>
                            <div class="col-md-9 col-12">
                                <div class="form-group">
                                    <label>Product Description</label>
                                    <textarea  name="description"  class="form-control" placeholder="Product Description">{{ $product->description }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12 seprator">
                                <hr>
                            </div>

                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Packing Size</label>
                                    <input value="{{ $product->packing_size }}" name="packing_size" type="text" class="form-control" placeholder="Packing" />
                                </div>
                            </div>

                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label class="control-label">Carton Size</label>
                                    <input name="carton_size" value="{{ $product->carton_size }}" type="number" class="form-control" placeholder="Carton Size" />
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>QC Required</label>
                                    <select name="qc_reuired" class="select2 form-control form-control-lg">
                                        <option @if($product->qc_reuired==0) selected @endif value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Part #</label>
                                    <input value="{{ $product->part }}" name="part" type="text" class="form-control" placeholder="Part #" />
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Width</label>
                                    <input value="{{ $product->width }}"  name="width" type="text" class="form-control" placeholder="Width" />
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Height</label>
                                    <input value="{{ $product->height }}"  name="height" type="text" class="form-control" placeholder="Height" />
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Length</label>
                                    <input value="{{ $product->length }}"  name="length" type="text" class="form-control" placeholder="Length" />
                                </div>
                            </div>


                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Color</label>
                                    <input value="{{ $product->color }}"  name="color" type="text" class="form-control" placeholder="Color" />
                                </div>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label>Weight</label>
                                    <input value="{{ $product->weight }}"  name="weight" type="text" class="form-control" placeholder="Weight" />
                                </div>
                            </div>



                            <div class="col-md-3 col-12 hide">
                                <div class="form-group">
                                    <label>Reorder Quantity</label>
                                    <input value="{{ $product->reorder_qty }}"  name="reorder_qty" value="0" type="text" class="form-control" placeholder="Reorder Quantity" />
                                </div>
                            </div>
                            <div class="col-md-3 col-12 hide">
                                <div class="form-group">
                                    <label>Minimum Quantity </label>
                                    <input value="{{ $product->minimum_qty }}" name="minimum_qty" value="0" type="text" class="form-control" placeholder="Minimum Quantity   " />
                                </div>
                            </div>
                            <div class="col-md-3 col-12 hide">
                                <div class="form-group">
                                    <label>Maximum Quantity</label>
                                    <input value="{{ $product->maximum_qty }}" name="maximum_qty" value="0" type="text" class="form-control" placeholder="Maximum Quantity" />
                                </div>
                            </div>




                            {{-- <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label class="control-label">Retail Price Per Piece</label>
                                    <input name="retail_price" value="{{$product->retail_price}}" type="text" class="form-control" placeholder="Retail Price" />
                                </div>
                            </div>

                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label class="control-label">Retail Price Per Box/Pouch</label>
                                    <input name="retail_price_packing" value="{{$product->retail_price_packing}}"  type="text" class="form-control" placeholder="Retail Price" />
                                </div>
                            </div>


                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label class="control-label">Retail Price Per Carton</label>
                                    <input name="retail_price_carton"  value="{{$product->retail_price_carton}}" type="text" class="form-control" placeholder="Retail Price" />
                                </div>
                            </div>

                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label class="control-label">Trade Price Per Piece</label>
                                    <input name="trade_price" type="text" value="{{$product->trade_price}}" class="form-control" placeholder="Trade Price" />
                                </div>
                            </div>

                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label class="control-label">Trade Price Per Box/Pouch</label>
                                    <input name="trade_price_packing" value="{{$product->trade_price_packing}}" type="text" class="form-control" placeholder="Trade Price" />
                                </div>
                            </div>


                            <div class="col-md-3 col-12">
                                <div class="form-group">
                                    <label class="control-label">Trade Price Per Carton</label>
                                    <input name="trade_price_carton" value="{{$product->trade_price_carton}}" type="text" class="form-control" placeholder="Trade Price" />
                                </div>
                            </div> --}}

                            <div class="col-md-3 col-12 hide">
                                <div class="form-group">
                                    <label>Sale Price</label>
                                    <input value="{{ $product->sales_price }}" name="sales_price" type="text" class="form-control" placeholder="Sale Price" />
                                </div>
                            </div>
                            <div class="col-md-12 seprator">
                                <hr>
                            </div>

                            <div class="col-md-12 col-12">
                                <div class="form-group">
                                    <label class="control-label">Product Image</label>
                                    <input name="image" type="file" class="form-control" accept="image/png, image/gif, image/jpeg , image/jpg" />
                                </div>
                            </div>


                            <div class="col-md-12 seprator">
                                <hr>
                            </div>

                            <div class="col-md-12">
                                <div class="main_head">
                                    <h2>Product Falvour</h2>
                                </div>
                            </div>

                            <div class="col-md-12 col-12 append_data">
                                <div class="form-group">
                                    <label class="control-label">Product Falvour</label>
                                    <button class="btn btn-sm btn-primary" type="button" onclick="addMore()">Add More</button>
                                </div>
                                <div class="form-group remove_data">
                                    @foreach ($product->product_flavour as $row)
                                    <input type="hidden" name="flavour_id[]" value="{{$row->id}}">
                                        <div class="row">
                                            <div class="col-md-3 col-3">
                                                <input  name="flavour_name[]" type="text" value="{{$row->flavour_name}}" class="form-control"/>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-12 seprator">
                                <hr>
                            </div>

                            <div class="col-md-12">
                                <div class="main_head">
                                    <h2>Product Price</h2>
                                </div>
                            </div>
                            <div class="col-md-12 col-12 append_price_data">
                                <div class="row remove_data">
                                    <div class="col-md-3 col-3">
                                        <label class="control-label">UOM</label>
                                    </div>
                                    <div class="col-md-3 col-3">
                                        <label class="control-label">Retail Price</label>
                                    </div>
                                    <div class="col-md-3 col-3">
                                        <label class="control-label">Trade Price</label>
                                    </div>
                                    <div class="col-md-3 col-3">
                                        <label class="control-label">Qty Per Carton</label>
                                    </div>
                                </div>
                                @php
                                    $price_counter = 0;
                                @endphp
                                <input type="hidden" name="delete_price_id" id="delete_price_id">
                                @foreach ($product->product_price as $row)

                                    <div class="row remove_data mb-2">
                                        <input type="hidden" name="product_price_id[]" value="{{$row->id}}">
                                        <div class="col-md-3 col-3">
                                            {{-- <label class="control-label">UOM</label> --}}
                                            {{-- <input type="text" class="form-control" readonly value="{{$master->uom_name($row->uom_id)}}"> --}}
                                            <select class="select2 form-control form-control-lg" name="uom_id[]" >
                                                <option value="">Select</option>
                                                @foreach ($uom_data as $key => $row1)
                                                <option value="{{ $row1->id }}" {{$row->uom_id == $row1->id ? 'selected' : ''}}>{{ $row1->uom_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3 col-3">
                                            {{-- <label class="control-label">Retail Price</label> --}}
                                            <input name="retail_price[]"  value="{{$row->retail_price}}" type="number" step="any" placeholder="Retail Price" class="form-control"/>
                                        </div>

                                        <div class="col-md-3 col-3">
                                            {{-- <label class="control-label">Trade Price</label> --}}
                                            <input name="trade_price[]" value="{{$row->trade_price}}"  type="number" step="any" placeholder="Trade Price" class="form-control"/>
                                        </div>
                                        <div class="col-md-2 col-2">
                                            <input name="pcs_per_carton[]" required type="number"  value="{{$row->pcs_per_carton}}" step="any" placeholder="Pieces Per Carton" class="form-control"/>
                                        </div>
                                        <div class="col-md-1 col-1">
                                            @if ($price_counter == 0)
                                                <button class="btn btn-sm btn-primary" type="button" onclick="addPriceMore()">Add More</button>
                                            @else
                                                <button class="btn btn-sm btn-danger" type="button" onclick="removeProductPrice(this , '{{$row->id}}')">Remove</button>
                                            @endif
                                        </div>
                                    </div>
                                    @php
                                        $price_counter++;
                                    @endphp
                                @endforeach
                            </div>


                            <div class="col-12">
                                <button type="submit" class="btn btn-primary mr-1">Create Item</button>
                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
                <!-- Basic Floating Label Form section end -->

@endsection

@section('script')
<script>
    function addMore()
    {
        html = `
        <div class="form-group remove_data">
            <div class="row">
                                            <div class="col-md-3 col-3">
                                                <input name="flavour_name[]" required type="text" class="form-control"/>
                                            </div>
                                            <div class="col-md-3 col-3">
                                                <button ttype="button" class="btn btn-sm btn-danger" onclick="removeMore(this)">Remove</button>
                                            </div>
                                            </div>
                                        </div>
        `;
        $('.append_data').append(html);
    }

    function removeMore(instance)
    {
        $(instance).closest('.remove_data').remove();

    }


    $(document).ready(function() {
    });
    var delete_price_id = [];

    function removeProductPrice(instance , rowId = null)
    {
        $(instance).closest('.remove_data').remove();
        if (rowId != null) {
            delete_price_id.push(rowId);

            // Update the hidden input with the JSON string of the array
            $('#delete_price_id').val(delete_price_id.join(','));

            console.log($('#delete_price_id').val(), delete_price_id);
        }

    }

    function addPriceMore()
    {
        html = `
         <div class="row mt-2 remove_data">
                                                <div class="col-md-3 col-3">
                                                    <select name="uom_id[]" class="select2 form-control form-control-lg">
                                                        <option value="">Select</option>
                                                        @foreach ($uom_data as $key => $row )
                                                        <option value="{{ $row->id }}">{{ $row->uom_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-3 col-3">
                                                    <input name="retail_price[]" required type="number" placeholder="Retail Price" step="any" class="form-control"/>
                                                </div>

                                                <div class="col-md-3 col-3">
                                                    <input name="trade_price[]" required type="number" placeholder="Trade Price" step="any" class="form-control"/>
                                                </div>
                                                <div class="col-md-2 col-2">
                                                    <input name="pcs_per_carton[]" required type="number" step="any" placeholder="Pieces Per Carton" class="form-control"/>
                                                </div>
                                                <div class="col-md-1 col-1">
                                                    <button class="btn btn-sm btn-danger" type="button" onclick="removeProductPrice(this)">Remove</button>
                                                </div>
                                            </div>
        `;
        $('.append_price_data').append(html);
    }

</script>
@endsection


