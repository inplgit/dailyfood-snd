@php
use App\Models\Product;
$product_data = new Product();
$uom_data = $product_data->get_all_uom();
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
                    <form id="subm" method="POST" action="{{ route('product.store') }}" class="form" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-2">
                                <div class="main_head">
                                    <h2>Product Details</h2>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="row">

                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Product Code <strong>*</strong></label>
                                            <input name="product_code" type="text" value="{{ $item_code }}" class="form-control" placeholder="Product Code"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Product Name <strong>*</strong></label>
                                            <input name="product_name" type="text" class="form-control" placeholder="Product Name"/>
                                        </div>
                                    </div>


                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Product Category <strong>*</strong></label>
                                            <select name="category_id" class="select2 form-control form-control-lg">
                                                <option value="">Select One</option>

                                                @foreach ( $product_data->get_all_category() as $key => $row )
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Product Type <strong>*</strong></label>
                                            <select name="product_type_id" class="select2 form-control form-control-lg">
                                                <option value="">Select</option>

                                                @foreach ($product_data->get_all_product_type() as $key => $row )
                                                <option value="{{ $row->id }}">{{ $row->type_name }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Brand <strong>*</strong></label>

                                            <select name="brand_id" class="select2 form-control form-control-lg">
                                                <option value="">Select</option>
                                                @foreach ($product_data->get_all_brand() as $key => $row )
                                                <option value="{{ $row->id }}">{{ $row->brand_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>



                                    {{-- <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Product Unit <strong>*</strong></label>
                                            <select name="uom_id" class="select2 form-control form-control-lg">
                                                <option value="">Select</option>
                                                @foreach ($uom_data as $key => $row )
                                                <option value="{{ $row->id }}">{{ $row->uom_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Packing Unit <strong>*</strong></label>
                                            <select name="packing_uom_id" class="select2 form-control form-control-lg">
                                                <option value="">Select</option>
                                                @foreach ($uom_data as $key => $row )
                                                <option value="{{ $row->id }}">{{ $row->uom_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> --}}


                                    <div class="col-md-3 col-12 mb-1">
                                        <div class="form-group">
                                            <label class="control-label">Locality</label>
                                            <select name="locality" class="select2 form-control form-control-lg">
                                                <option value="0">Local</option>
                                                <option value="1">Imported</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">SKU <strong>*</strong></label>
                                            <input name="SKU" type="text" class="form-control" placeholder="SKU" />
                                        </div>
                                    </div>
                                    <div class="col-md-9 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Product Description</label>
                                            <textarea  name="description"  class="form-control" placeholder="Product Description"></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-12 seprator">
                                        <hr>
                                    </div>

                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Packing Size</label>
                                            <input name="packing_size" type="number" class="form-control" placeholder="Packing Size" />
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Carton Size</label>
                                            <input name="carton_size" type="number" class="form-control" placeholder="Carton Size" />
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">QC Required</label>
                                            <select name="qc_reuired" class="select2 form-control form-control-lg">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Part #</label>
                                            <input name="part" type="text" class="form-control" placeholder="Part #" />
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Width</label>
                                            <input name="width" type="text" class="form-control" placeholder="Width" />
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Height</label>
                                            <input name="height" type="text" class="form-control" placeholder="Height" />
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Length</label>
                                            <input name="length" type="text" class="form-control" placeholder="Length" />
                                        </div>
                                    </div>


                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Color</label>
                                            <input name="color" type="text" class="form-control" placeholder="Color" />
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Weight</label>
                                            <input name="weight" type="text" class="form-control" placeholder="Weight" />
                                        </div>
                                    </div>



                                    <div class="col-md-3 col-12 hide">
                                        <div class="form-group">
                                            <label class="control-label">Reorder Quantity</label>
                                            <input name="reorder_qty" value="0" type="text" class="form-control" placeholder="Reorder Quantity" />
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12 hide">
                                        <div class="form-group">
                                            <label class="control-label">Minimum Quantity </label>
                                            <input name="minimum_qty" value="0" type="text" class="form-control" placeholder="Minimum Quantity   " />
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12 hide">
                                        <div class="form-group">
                                            <label class="control-label">Maximum Quantity</label>
                                            <input name="maximum_qty" value="0" type="text" class="form-control" placeholder="Maximum Quantity" />
                                        </div>
                                    </div>


                                    {{-- <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Retail Price Per Piece</label>
                                            <input name="retail_price" type="text" class="form-control" placeholder="Retail Price" />
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Retail Price Per Box/Pouch</label>
                                            <input name="retail_price_packing" type="text" class="form-control" placeholder="Retail Price" />
                                        </div>
                                    </div>


                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Retail Price Per Carton</label>
                                            <input name="retail_price_carton" type="text" class="form-control" placeholder="Retail Price" />
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Trade Price Per Piece</label>
                                            <input name="trade_price" type="text" class="form-control" placeholder="Trade Price" />
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Trade Price Per Box/Pouch</label>
                                            <input name="trade_price_packing" type="text" class="form-control" placeholder="Trade Price" />
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <label class="control-label">Trade Price Per Carton</label>
                                            <input name="trade_price_carton" type="text" class="form-control" placeholder="Trade Price" />
                                        </div>
                                    </div> --}}
                                    <div class="col-md-3 col-12 hide">
                                        <div class="form-group">
                                            <label class="control-label">Sale Price</label>
                                            <input name="sales_price" type="text" class="form-control" placeholder="Sale Price" />
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
                                        {{-- <div class="form-group">
                                            <label class="control-label">Product Falvour</label>
                                            <button class="btn btn-sm btn-primary" type="button" onclick="addMore()">Add More</button>
                                        </div> --}}
                                        {{-- <div class="form-group remove_data"> --}}
                                            <div class="row remove_data">
                                                <div class="col-md-3 col-3">
                                                <label class="control-label">Falvour Name</label>
                                                    <input name="flavour_name[]" placeholder="Flavour Name" required type="text" class="form-control"/>
                                                </div>
                                                <div class="col-md-3 col-3">
                                                    <button class="btn btn-sm btn-primary" type="button" onclick="addMore()">Add More</button>
                                                </div>
                                            </div>
                                        {{-- </div> --}}
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
                                                    <select name="uom_id[]" class="select2 form-control form-control-lg">
                                                        <option value="">Select</option>
                                                        @foreach ($uom_data as $key => $row )
                                                        <option value="{{ $row->id }}">{{ $row->uom_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-3 col-3">
                                                    <label class="control-label">Retail Price</label>
                                                    <input name="retail_price[]" required type="number" step="any" placeholder="Retail Price" class="form-control"/>
                                                </div>

                                                <div class="col-md-3 col-3">
                                                    <label class="control-label">Trade Price</label>
                                                    <input name="trade_price[]" required type="number" step="any" placeholder="Trade Price" class="form-control"/>
                                                </div>
                                                <div class="col-md-2 col-2">
                                                    <label class="control-label">Qty Per Carton</label>
                                                    <input name="pcs_per_carton[]" required type="number" step="any" placeholder="Pieces Per Carton" class="form-control"/>
                                                </div>
                                                <div class="col-md-1 col-1">
                                                    <button class="btn btn-sm btn-primary" type="button" onclick="addPriceMore()">Add More</button>
                                                </div>
                                            </div>
                                    </div>


                                </div>
                            </div>

                            <div class="col-md-12 text-right">
                                <div class="button_create">
                                    <button type="submit" class="btn btn-primary mr-1">Create Item</button>
                                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                </div>
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
            <div class="row mt-2 remove_data">
                <div class="col-md-3 col-3">
                    <input name="flavour_name[]" required type="text" placeholder="Flavour Name" class="form-control"/>
                </div>
                <div class="col-md-3 col-3">
                    <button ttype="button" class="btn btn-sm btn-danger" onclick="removeMore(this)">Remove</button>
                </div>
            </div>
        `;
        $('.append_data').append(html);
    }

    function removeMore(instance)
    {
        $(instance).closest('.remove_data').remove();
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
                                                    <button class="btn btn-sm btn-danger" type="button" onclick="removeMore(this)">Remove</button>
                                                </div>
                                            </div>
        `;
        $('.append_price_data').append(html);
    }

</script>
@endsection
