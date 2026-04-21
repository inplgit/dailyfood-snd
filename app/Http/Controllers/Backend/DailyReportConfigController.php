<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyReportConfig;
use App\Models\City;
use App\Services\DailyReportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use App\Mail\DailyReportMail;

class DailyReportConfigController extends Controller
{
    public function index()
    {
        $config = DailyReportConfig::first();
        $cities = City::where('status', 1)->get();
        $designations = \App\Models\Designation::where('status', 1)->get(); // 1 is active
        return view('pages.Settings.daily_report', compact('config', 'cities', 'designations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cc_emails' => 'nullable|string',
            'city_ids' => 'nullable|array',
            'city_ids.*' => 'integer',
            'city_emails' => 'nullable|array',
            'designation_ids' => 'nullable|array',
            'designation_ids.*' => 'integer',
            'zero_sale_designation_ids' => 'nullable|array',
            'zero_sale_designation_ids.*' => 'integer',
        ]);

        $config = DailyReportConfig::first() ?? new DailyReportConfig();
        
        $config->cc_emails = $request->input('cc_emails');
        $config->city_ids = $request->input('city_ids', []);
        $config->city_emails = $request->input('city_emails', []);
        $config->designation_ids = $request->input('designation_ids', []);
        $config->zero_sale_designation_ids = $request->input('zero_sale_designation_ids', []);
        $config->show_tso_attendance = $request->has('show_tso_attendance');
        $config->show_distributor_sales = $request->has('show_distributor_sales');
        $config->show_product_sales = $request->has('show_product_sales');
        $config->show_top_bottom_tso = $request->has('show_top_bottom_tso');
        $config->show_top_bottom_shop = $request->has('show_top_bottom_shop');
        $config->show_overall_sales = $request->has('show_overall_sales');
        $config->show_zero_sale_tso = $request->has('show_zero_sale_tso');
        $config->is_active = $request->has('is_active');
        
        $config->save();

        return redirect()->back()->with('success', 'Daily Report Settings updated successfully.');
    }

    public function downloadPdf(DailyReportService $reportService)
    {
        $config = DailyReportConfig::first();
        if (!$config) {
             return redirect()->back()->with('error', 'Please configure and save settings first.');
        }

        $dateStr = Carbon::today()->format('Y-m-d');
        $pdfContent = $reportService->generatePdfContent($config, $dateStr);

        $filename = "Daily_Report_{$dateStr}.pdf";
        return response()->streamDownload(
            fn () => print($pdfContent),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function downloadCityPdf(DailyReportService $reportService, $cityId)
    {
        $config = DailyReportConfig::first();
        if (!$config) {
             return redirect()->back()->with('error', 'Please configure settings first.');
        }

        $dateStr = Carbon::today()->format('Y-m-d');
        $pdfContent = $reportService->generatePdfContentForCity($config, $dateStr, $cityId);

        $city = City::find($cityId);
        $cityName = $city ? $city->name : 'City';
        $filename = "Daily_Report_{$cityName}_{$dateStr}.pdf";
        
        return response()->streamDownload(
            fn () => print($pdfContent),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function sendCityNow(DailyReportService $reportService, $cityId)
    {
        $config = DailyReportConfig::first();
        if (!$config) {
             return redirect()->back()->with('error', 'Please configure settings first.');
        }

        $allCcEmails = !empty($config->cc_emails) ? array_map('trim', explode(',', $config->cc_emails)) : [];
        
        $dateStr = Carbon::today()->format('Y-m-d');
        $cityEmailsMapping = $config->city_emails ?? [];

        $citySpecificEmails = !empty($cityEmailsMapping[$cityId]) 
            ? array_map('trim', explode(',', $cityEmailsMapping[$cityId])) 
            : [];

        if (empty($citySpecificEmails)) {
            return redirect()->back()->with('error', 'No target emails configured for this city.');
        }

        $pdfContent = $reportService->generatePdfContentForCity($config, $dateStr, $cityId);
        Mail::to($citySpecificEmails)->cc($allCcEmails)->send(new DailyReportMail($pdfContent, $dateStr, $cityId, $allCcEmails));
        
        return redirect()->back()->with('success', 'City-wise Report Sent Successfully.');
    }

    public function sendNow(DailyReportService $reportService)
    {
        $config = DailyReportConfig::first();
        if (!$config) {
             return redirect()->back()->with('error', 'Please configure settings first.');
        }
        
        $allCcEmails = !empty($config->cc_emails) ? array_map('trim', explode(',', $config->cc_emails)) : [];
        
        $dateStr = Carbon::today()->format('Y-m-d');
        $cityIds = $config->city_ids ?? [];
        $cityEmailsMapping = $config->city_emails ?? [];

        if (empty($cityIds)) {
            // Fallback: Send single consolidated report to CC list if no cities selected
            $pdfContent = $reportService->generatePdfContent($config, $dateStr);
            Mail::to($allCcEmails)->send(new DailyReportMail($pdfContent, $dateStr, null, []));
        } else {
            foreach ($cityIds as $cityId) {
                $citySpecificEmails = !empty($cityEmailsMapping[$cityId]) 
                    ? array_map('trim', explode(',', $cityEmailsMapping[$cityId])) 
                    : [];

                if (!empty($citySpecificEmails)) {
                    $pdfContent = $reportService->generatePdfContentForCity($config, $dateStr, $cityId);
                    Mail::to($citySpecificEmails)->cc($allCcEmails)->send(new DailyReportMail($pdfContent, $dateStr, $cityId, $allCcEmails));
                }
            }
        }
        
        return redirect()->back()->with('success', 'Daily Reports Sent Successfully (City-wise).');
    }
}
