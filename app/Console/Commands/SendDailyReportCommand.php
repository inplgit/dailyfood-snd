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

        if (!$config) {
            $this->info('No active daily report configuration found.');
            return 0;
        }

        $allCcEmails = !empty($config->cc_emails)
            ? array_map('trim', explode(',', $config->cc_emails))
            : [];

        $dateStr = Carbon::today()->format('Y-m-d');
        $cityIds = $config->city_ids ?? [];
        $cityEmailsMapping = $config->city_emails ?? [];

        if (empty($cityIds)) {
            // ✅ Fallback: send consolidated report
            $pdfContent = $reportService->generatePdfContent($config, $dateStr);

            if (!empty($allCcEmails)) {
                Mail::to($allCcEmails)
                    ->send(new DailyReportMail($pdfContent, $dateStr, null, []));
            }

            $this->info('Consolidated report sent to CC emails.');
        } else {
            foreach ($cityIds as $cityId) {

                $citySpecificEmails = !empty($cityEmailsMapping[$cityId])
                    ? array_map('trim', explode(',', $cityEmailsMapping[$cityId]))
                    : [];

                if (!empty($citySpecificEmails)) {

                    $pdfContent = $reportService->generatePdfContentForCity(
                        $config,
                        $dateStr,
                        $cityId
                    );

                    Mail::to($citySpecificEmails)
                        ->cc($allCcEmails)
                        ->send(new DailyReportMail(
                            $pdfContent,
                            $dateStr,
                            $cityId,
                            $allCcEmails
                        ));

                    $this->info("Report sent for city ID: {$cityId}");
                }
            }
        }

        $this->info('Daily Reports Sent Successfully (City-wise).');
        return 0;
    }
}
