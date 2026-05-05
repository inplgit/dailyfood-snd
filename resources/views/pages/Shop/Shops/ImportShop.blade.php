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

                    {{-- Progress Bar --}}
                    <div id="import-progress-container" style="display: none;">
                        <div class="progress progress-bar-primary" style="height: 25px;">
                            <div id="import-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                                 aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">0%</div>
                        </div>
                        <p id="import-message" class="text-center mt-1">Processing...</p>
                    </div>

                    {{-- ✅ Form Start --}}
                    <form id="import-form" method="POST" action="{{ route('shop.import_shops_store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="import_id" id="import_id" value="{{ time() }}">
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
                                                <input type="file" name="file" class="form-control" required>
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
                                <button type="submit" id="submit-btn" class="btn btn-primary">Import</button>
                            </div>
                        </div> {{-- row --}}
                    </form>
                    {{-- ✅ Form End --}}
                </div>
            </div>
        </div>
    </div>
</section>

@section('script')
<script>
    $(document).ready(function() {
        $('#import-form').on('submit', function(e) {
            $('#import-progress-container').show();
            $('#import-form').hide();
            $('#submit-btn').prop('disabled', true);
            
            var importId = $('#import_id').val();
            var interval = setInterval(function() {
                $.ajax({
                    url: "{{ route('shop.import_status', '') }}/" + importId,
                    type: "GET",
                    success: function(data) {
                        if (data.progress) {
                            $('#import-progress-bar').css('width', data.progress + '%');
                            $('#import-progress-bar').text(data.progress + '%');
                            $('#import-progress-bar').attr('aria-valuenow', data.progress);
                        }
                        if (data.message) {
                            $('#import-message').text(data.message);
                        }
                        if (data.status === 'completed') {
                            clearInterval(interval);
                        }
                    },
                    error: function() {
                    }
                });
            }, 2000);
        });
    });
</script>
@endsection
@endsection
