@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="page-title">Daily Report Settings (Consolidated)</h4>
            </div>
            <div class="card-body">

                <form method="POST" action="{{ route('settings.daily_report.store') }}">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="emails" class="form-label">Recipient Emails</label>
                            <input type="text" class="form-control" id="emails" name="emails" value="{{ old('emails', $config->emails ?? '') }}" placeholder="email1@example.com, email2@example.com" required>
                            <small class="text-muted">Enter multiple email addresses separated by commas.</small>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch form-switch-lg mt-3">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $config->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Enable Daily Automated Email (at 8:00 PM)</label>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">Select Cities to Include in Report</h5>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="border p-3 rounded" style="max-height: 200px; overflow-y: auto; background-color: #f8f9fa;">
                                @php
                                    $selectedCities = $config->city_ids ?? [];
                                @endphp
                                <div class="row">
                                    @foreach($cities as $city)
                                        <div class="col-md-3 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="city_ids[]" id="city_{{ $city->id }}" value="{{ $city->id }}" {{ in_array($city->id, $selectedCities) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="city_{{ $city->id }}">
                                                    {{ $city->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <small class="text-muted">If no cities are selected, the report will show "All Cities" data combined.</small>
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">Report Sections to Include</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="show_tso_attendance" id="show_tso_attendance" value="1" {{ old('show_tso_attendance', $config->show_tso_attendance ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_tso_attendance">TSO Attendance & Absent List</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="show_distributor_sales" id="show_distributor_sales" value="1" {{ old('show_distributor_sales', $config->show_distributor_sales ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_distributor_sales">Total Distributor Sales</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="show_product_sales" id="show_product_sales" value="1" {{ old('show_product_sales', $config->show_product_sales ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_product_sales">Total Product Sales (Qty)</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="show_top_bottom_tso" id="show_top_bottom_tso" value="1" {{ old('show_top_bottom_tso', $config->show_top_bottom_tso ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_top_bottom_tso">Top 10 / Bottom 10 TSOs</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="show_top_bottom_shop" id="show_top_bottom_shop" value="1" {{ old('show_top_bottom_shop', $config->show_top_bottom_shop ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_top_bottom_shop">Top 10 / Bottom 10 Shops</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="show_overall_sales" id="show_overall_sales" value="1" {{ old('show_overall_sales', $config->show_overall_sales ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_overall_sales">Grand Total Summary Section</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 border-top pt-3">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                        
                        @if($config)
                            <a href="{{ route('settings.daily_report.download') }}" class="btn btn-success ms-2">
                                <i class="fa fa-download me-1"></i> Download PDF
                            </a>
                            
                            <button type="submit" formaction="{{ route('settings.daily_report.send_now') }}" class="btn btn-dropbox ms-2">
                                <i class="fa fa-paper-plane me-1"></i> Send Email Now
                            </button>
                        @endif
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
