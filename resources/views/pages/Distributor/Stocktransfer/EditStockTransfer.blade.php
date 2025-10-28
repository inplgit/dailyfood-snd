@php
    use App\Helpers\MasterFormsHelper;
    $master = new MasterFormsHelper();
@endphp
@extends('layouts.master')
@section('title', 'Add Zone')
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Stock Transfer</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('stock.store') }}" id="subm" class="form">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="distributor_id">From Distributor</label>
                                        <select name="distributor_id" id="distributor_id" class="form-control">
                                            <option value="">--Select--</option>
                                            @foreach (MasterFormsHelper::get_all_distributor_user_wise() as $distributor)
                                                <option @if($stock[0]->distributor_id==$distributor->id) selected @endif value="{{ $distributor->id }}">{{ $distributor->distributor_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="distributor_id">To Distributor</label>
                                        <select name="distributor_id_to" id="distributor_id" class="form-control">
                                            <option value="">--Select--</option>
                                            @foreach (MasterFormsHelper::get_all_distributor_user_wise() as $distributor)
                                                <option @if($stock[0]->distributor_sole==$distributor->id) selected @endif value="{{ $distributor->id }}">{{ $distributor->distributor_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="voucher_date">Date</label>
                                        <input type="date" value="{{ $stock[0]->voucher_date }}"  name="voucher_date" id="voucher_date" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="remarks">Remarks</label>
                                        <textarea name="remarks" id="remarks" class="form-control">{{ $stock[0]->remarks }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th style="width: 50%;">Products</th>
                                                <th style="width: 40%;">Quantity</th>
                                                <th style="width: 10%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="productDetail">
                                        @foreach($stock as $key=> $row)
                                            <tr>
                                                <td scope="row">
                                                    <select name="product_id[]" class="form-control">
                                                        @foreach (MasterFormsHelper::get_all_product() as $product)
                                                            <option @if($row->product_id==$product->id) selected @endif value="{{ $product->id }}">{{ $product->product_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number"  name="qty[]" id="" value="{{ $row->qty }}"
                                                        step="0.01" required class="form-control">
                                                </td>
                                                <td>
                                                    <button type="button" onclick="addMore()" class="btn btn-primary btn-xs">ADD MORE</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
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
        let counter = 0;
        function addMore() {
            $('#productDetail').append(`
            <tr id="removeRow${++counter}">
                <td scope="row">
                    <select name="product_id[]" class="form-control">
                        @foreach (MasterFormsHelper::get_all_product() as $product)
                            <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="qty[]" id="" value="0" step="0.01" required class="form-control">
                </td>
                <td>
                    <button type="button" onClick="removeRow(${counter})" class="btn btn-danger btn-xs">REMOVE</button>
                </td>
            </tr>
            `)
        }
        function removeRow(params) {
            $('#removeRow' +params).remove();
        }
    </script>
@endsection
