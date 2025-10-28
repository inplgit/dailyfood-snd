@php
    use App\Helpers\MasterFormsHelper;
@endphp
@extends('layouts.master')
@section('title', 'Import Stock (CSV)')
@section('content')
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">IMPORT STOCK (CSV)</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('stockManagement.uploadStockFIle') }}" class="form"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    @if(Session::has('catchError'))
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ Session::get('catchError') }}</strong>
                                        </span>
                                    @endif
                                    <table class="table table-bordered table-striped table-condensed">
                                        <tbody>
                                            <tr>
                                                <td>Sample Import File (CSV format)</td>
                                                <td>
                                                    <a href="{{asset('public/assets/format/stock_import.xlsx')}}" download="">Sample
                                                        File
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-bordered table-striped table-condensed">
                                        <tbody>
                                            <tr>
                                                <td> Date </td>
                                                <td>
                                                    <input name="voucher_date" type="date" id="date"
                                                        class="form-control" autocomplete="new-password">
                                                    @error('voucher_date')
                                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </td>
                                            </tr>
                                            <tr>
                                                <td> Stock File </td>
                                                <td>
                                                    <input type="file" name="file"
                                                        class="form-control">
                                                    @error('file')
                                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Stock Type </td>
                                                <td>
                                                    <select name="stock_type" class="form-control">
                                                        <option value="1">Opening stock</option>
                                                        <option value="2" selected="">Stock from principle</option>
                                                    </select>
                                                    @error('stock_type')
                                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary mr-1">Create Item</button>
                                </div>
                                <div class="col-6">
                                    @if(Session::has('proNotExist'))
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{'This Product SKU is not exists '. implode(',', Session::get('proNotExist')) }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-6">
                                    @if(Session::has('distriNotExist'))
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{'This Distributors Code is not exists '. implode(',', Session::get('distriNotExist')) }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
