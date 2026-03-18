<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DailyReportConfig;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\DailyReportMail;
use App\Services\DailyReportService;

class SendDailyReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and send the daily PDF report based on configuration settings.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(DailyReportService $reportService)
    {
        $config = DailyReportConfig::where('is_active', 1)->first();

        if (!$config || empty($config->emails)) {
            $this->info('No active daily report configuration found or no emails set.');
            return 0;
        }

        $dateStr = Carbon::today()->format('Y-m-d');
        $emails = array_map('trim', explode(',', $config->emails));
        
        // Generate PDF
        $pdfContent = $reportService->generatePdfContent($config, $dateStr);

        // Send Email to all recipients
        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($email)->send(new DailyReportMail($pdfContent, $dateStr));
            }
        }

        $this->info("Consolidated daily report sent to: " . implode(', ', $emails));
        return 0;
    }
}
