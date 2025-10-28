
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
                        <h4 class="card-title">IMPORT Product</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('product.import_product_store') }}" class="form"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                {{-- <input type="hidden" name="tso_id" value="{{ $id }}"/> --}}
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
                                                    <a href="{{asset('public/assets/format/product_import.xlsx')}}" download="">Sample
                                                        File
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <table class="table table-bordered table-striped table-condensed">
                                        <tbody>

                                            <tr>
                                                <td> Product File </td>
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

                                        </tbody>
                                    </table>
                                </div>
                                <br>
                                <div class="col-12 right d-flex p-2">
                                    <button type="submit" class="btn btn-primary mr-1">Import</button>
                                </div>
                                <div class="col-4">
                                    @if(Session::has('ProductExistis'))
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{'This Product Name is already exists '. implode(',', Session::get('ProductExistis')) }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-4">
                                    @if(Session::has('UOMNotExist'))
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{'This UOM Name is not exists '. implode(',', Session::get('UOMNotExist')) }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-4">
                                    @if(Session::has('UomPackingNotExist'))
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{'This UOM weight is not exists '. implode(',', Session::get('UomWeightNotExist')) }}</strong>
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

