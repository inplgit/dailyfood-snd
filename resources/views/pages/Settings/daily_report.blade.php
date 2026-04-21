@extends('layouts.master')
@section('title', 'Daily Report Settings')

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
                        <div class="col-md-12">
                            <label for="cc_emails" class="form-label">CC Emails (Dynamic)</label>
                            <input type="text" class="form-control" id="cc_emails" name="cc_emails" value="{{ old('cc_emails', $config->cc_emails ?? '') }}" placeholder="cc1@example.com, cc2@example.com" autocomplete="off">
                            <small class="text-muted">Will be added to EVERY city-wise email.</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12 d-flex align-items-center">
                            <div class="form-check form-switch form-switch-lg">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $config->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Enable Daily Automated Email (at 11:30 PM)</label>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">Select Cities & Enter Target Emails</h5>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="border p-3 rounded" style="max-height: 400px; overflow-y: auto; background-color: #f8f9fa;">
                                @php
                                    $selectedCities = $config->city_ids ?? [];
                                    $cityEmails = $config->city_emails ?? [];
                                @endphp
                                <table class="table table-sm table-borderless">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">Select</th>
                                            <th style="width: 150px;">City Name</th>
                                            <th>Target Emails</th>
                                            <th class="text-center" style="width: 250px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cities as $city)
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="city_ids[]" id="city_{{ $city->id }}" value="{{ $city->id }}" {{ in_array($city->id, $selectedCities) ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                                <td>
                                                    <label class="form-check-label" for="city_{{ $city->id }}">
                                                        {{ $city->name }}
                                                    </label>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="city_emails[{{ $city->id }}]" value="{{ $cityEmails[$city->id] ?? '' }}" placeholder="asm1@example.com, asm2@example.com" autocomplete="off">
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('settings.daily_report.download_city', $city->id) }}" class="btn btn-sm btn-outline-success" title="Download This City PDF">
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                        <button type="submit" formaction="{{ route('settings.daily_report.send_city_now', $city->id) }}" class="btn btn-sm btn-outline-info" title="Send This City Now">
                                                            <i class="fa fa-paper-plane"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <small class="text-muted">Only checked cities will be included in the report. If no specific emails are entered for a checked city, it will fallback to the Recipient Emails list above.</small>
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">Select Designations for Attendance Report</h5>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="border p-3 rounded" style="max-height: 200px; overflow-y: auto; background-color: #f8f9fa;">
                                @php
                                    $selectedDesignations = $config->designation_ids ?? [];
                                @endphp
                                <div class="row">
                                    @foreach($designations as $designation)
                                        <div class="col-md-3 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="designation_ids[]" id="desig_{{ $designation->id }}" value="{{ $designation->id }}" {{ in_array($designation->id, $selectedDesignations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="desig_{{ $designation->id }}">
                                                    {{ $designation->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <small class="text-muted">Only selected designations will be included in the "Daily Attendance" section of the report.</small>
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">Select Designations for Zero Sale TSO Report</h5>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="border p-3 rounded" style="max-height: 200px; overflow-y: auto; background-color: #f8f9fa;">
                                @php
                                    $selectedZeroDesig = $config->zero_sale_designation_ids ?? [];
                                @endphp
                                <div class="row">
                                    @foreach($designations as $designation)
                                        <div class="col-md-3 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="zero_sale_designation_ids[]" id="zero_desig_{{ $designation->id }}" value="{{ $designation->id }}" {{ in_array($designation->id, $selectedZeroDesig) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="zero_desig_{{ $designation->id }}">
                                                    {{ $designation->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <small class="text-muted">Only selected designations will be included in the "Zero Sale TSOs" section of the report.</small>
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
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="show_zero_sale_tso" id="show_zero_sale_tso" value="1" {{ old('show_zero_sale_tso', $config->show_zero_sale_tso ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_zero_sale_tso">Zero Sale TSOs (Today)</label>
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
