@php
    use App\Helpers\MasterFormsHelper;
@endphp

@extends('layouts.master')
@section('title', 'Import Shop (CSV)')
@section('content')
<section id="multiple-column-form">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">IMPORT SHOP (CSV)</h4>
                </div>
                <div class="card-body">

                    {{-- ✅ Flash Messages --}}
                    @if (session('success'))
                        <div class="alert alert-success">
                            <strong>{{ session('success') }}</strong>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            <strong>{{ session('error') }}</strong>
                        </div>
                    @endif

                    @if (session('catchError'))
                        <div class="alert alert-danger">
                            <strong>{{ session('catchError') }}</strong>
                        </div>
                    @endif

                    @if (session('invalid_rows'))
                        <div class="alert alert-danger">
                            <strong>Invalid TSO or Distributor Entries:</strong>
                            <ul>
                                @foreach (session('invalid_rows') as $error)
                                    <li>
                                        Row {{ $error['row'] }}: {{ $error['message'] }}
                                        (Distributor: {{ $error['distributor_code'] }}, TSO(s): {{ $error['tso_codes'] }})
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('exists'))
                        <div class="alert alert-warning">
                            <strong>Already Existing Shops:</strong>
                            <ul>
                                @foreach (session('exists') as $name)
                                    <li>{{ $name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('formatNotMatch'))
                        <div class="alert alert-danger">
                            <strong>Format Not Matched in Sheet(s):</strong>
                            <ul>
                                @foreach (session('formatNotMatch') as $sheet)
                                    <li>{{ $sheet }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- ✅ Form Start --}}
                    <form method="POST" action="{{ route('shop.import_shops_store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                            <div class="col-md-12">
                                {{-- Sample File Download --}}
                                <table class="table table-bordered table-striped table-condensed">
                                    <tbody>
                                        <tr>
                                            <td>Sample Import File (CSV format)</td>
                                            <td>
                                                <a href="{{ asset('public/assets/format/shop_import.xlsx') }}" download>
                                                    Sample File
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                {{-- File Input --}}
                                <table class="table table-bordered table-striped table-condensed">
                                    <tbody>
                                        <tr>
                                            <td>Shops File</td>
                                            <td>
                                                <input type="file" name="file" class="form-control">
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

                            <div class="col-12 d-flex justify-content-end p-2">
                                <button type="submit" class="btn btn-primary">Import</button>
                            </div>

                        </div> {{-- row --}}
                    </form>
                    {{-- ✅ Form End --}}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
