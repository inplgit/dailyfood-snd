@php
    use App\Helpers\MasterFormsHelper;
    use App\Models\Product;
    $master = new MasterFormsHelper();
    $id = Request::get('id');
    $v_date = '';
    if (!empty($stock)):
    $v_date = $stock[0]->voucher_date;
    endif;
@endphp
@extends('layouts.master')
@section('title', 'Add Opening')
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add Opening</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('insert_opening') }}" id="subm" class="form">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="distributor_id">From Distributor</label>
                                        <select onchange="get_stock_data()" name="distributor_id" id="distributor_id" class="form-control">
                                            <option value="">--Select--</option>
                                            @foreach (MasterFormsHelper::get_all_distributor_user_wise() as $distributor)
                                                <option @if($id == $distributor->id) selected @endif value="{{ $distributor->id }}">{{ $distributor->distributor_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="distributor_id">Opening Date</label>
                                        <input name="open_date" type="date" value="{{ $v_date }}" name="v_date" class="form-control"/>
                                    </div>


                                    <div class="row" id="table-bordered">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">

                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Sr No</th>
                                                                <th>Product Name</th>
                                                                <th colspan="5">Product Description</th>
                                                                {{-- <th>Product Flavour</th>
                                                                <th>Opening QTY</th> --}}

                                                            </tr>
                                                        </thead>
                                                        <tbody id="">
                                                            @if(!empty($stock))

                                                            @foreach ( $stock as $key => $row )
                                                            @php
                                                                $product = Product::find($row->id);
                                                                $flavour_count = ($product->product_flavour->count()) + 1;
                                                                // dump($flavour_count);
                                                            @endphp

                                                            <tr>
                                                                <td rowspan="{{$flavour_count}}">{{ ++$key }}</td>
                                                                <td rowspan="{{$flavour_count}}">{{ $row->product_name }}</td>
                                                                <td>Product Flavour</td>
                                                                @foreach ($product->product_price as $productPrice)
                                                                    <td>{{$master->uom_name($productPrice->uom_id)}}</td>
                                                                @endforeach
                                                                @foreach ($product->product_flavour as $product_flavour)
                                                                    <tr>
                                                                        <td>
                                                                            {{ $product_flavour->flavour_name }}
                                                                        </td>
                                                                        @foreach ($product->product_price as $product_price)
                                                                        <td>
                                                                            @php
                                                                                $qty = MasterFormsHelper::get_Opining_Stock($row->id , $product_flavour->id , $product_price->uom_id , $row->distributor_id);
                                                                            @endphp
                                                                           <input class="form-control" name="qty[{{$row->id}}][{{$product_flavour->id}}][{{$product_price->uom_id}}]" 
       type="number" step="any" value="{{ $qty }}"/>
<input class="form-control" name="product_id[]" type="hidden" value="{{ $row->id }}"/>

                                                                        </td>

                                                                        @endforeach
                                                                    </tr>
                                                                @endforeach
                                                                {{-- <td rowspan="{{$flavour_count}}">
                                                                    <input class="form-control" name="qty[]" type="number" step="any" value="{{ $row->qty }}"/>
                                                                    <input class="form-control" name="product_id[]" type="hidden" step="any" value="{{ $row->id }}"/>
                                                                </td> --}}
                                                            </tr>

                                                            @endforeach
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>







                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary mr-1">Create Stock</button>
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
        function get_stock_data()
        {
            var distributor_id = $('#distributor_id').val();
            window.location.href = "{{url('opening/add_opening_form?id=')}}"+distributor_id;
        }

        $(document).ready(function() {
          $('#distributor_id').select2();

        });
    </script>


@endsection
