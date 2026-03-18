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
        return view('pages.Settings.daily_report', compact('config', 'cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'emails' => 'required|string',
            'city_ids' => 'nullable|array',
            'city_ids.*' => 'integer',
        ]);

        $config = DailyReportConfig::first() ?? new DailyReportConfig();
        
        $config->emails = $request->input('emails');
        $config->city_ids = $request->input('city_ids', []);
        $config->show_tso_attendance = $request->has('show_tso_attendance');
        $config->show_distributor_sales = $request->has('show_distributor_sales');
        $config->show_product_sales = $request->has('show_product_sales');
        $config->show_top_bottom_tso = $request->has('show_top_bottom_tso');
        $config->show_top_bottom_shop = $request->has('show_top_bottom_shop');
        $config->show_overall_sales = $request->has('show_overall_sales');
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

    public function sendNow(DailyReportService $reportService)
    {
        $config = DailyReportConfig::first();
        if (!$config || empty($config->emails)) {
             return redirect()->back()->with('error', 'Please configure recipient emails first.');
        }

        $dateStr = Carbon::today()->format('Y-m-d');
        $pdfContent = $reportService->generatePdfContent($config, $dateStr);
        $emailsArray = array_map('trim', explode(',', $config->emails));
        
        Mail::to($emailsArray)->send(new DailyReportMail($pdfContent, $dateStr));
        
        return redirect()->back()->with('success', 'Daily Report Sent Successfully.');
    }
}
