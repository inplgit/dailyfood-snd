
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
                        <h4 class="card-title">IMPORT TSO</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('tso.import_tso_store') }}" class="form"
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
                                                    <a href="{{asset('public/assets/format/tso_import.xlsx')}}" download="">Sample
                                                        File
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <table class="table table-bordered table-striped table-condensed">
                                        <tbody>

                                            <tr>
                                                <td> TSO File </td>
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
                                    @if(Session::has('exists'))
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{'This TSO User is already exists '. implode(',', Session::get('exists')) }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-4">
                                    @if(Session::has('tsoNotMatch'))
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{'The TSO user was not inserted due to a lack of matching distributors '. implode(',', Session::get('tsoNotMatch')) }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-4">
                                    @if(Session::has('DistributorNotExist'))
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{'This Distributors Name is not exists '. implode(',', Session::get('DistributorNotExist')) }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-4">
                                    @if(Session::has('designationNotExist'))
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{'This designations Name is not exists '. implode(',', Session::get('designationNotExist')) }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-4">
                                    @if(Session::has('managerNotExist'))
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{'This manager Name is not exists '. implode(',', Session::get('managerNotExist')) }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-4">
                                    @if(Session::has('departmentNotExist'))
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{'This departments Name is not exists '. implode(',', Session::get('departmentNotExist')) }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-4">
                                    @if(Session::has('userRole'))
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{'This user role Name is not exists '. implode(',', Session::get('userRole')) }}</strong>
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

