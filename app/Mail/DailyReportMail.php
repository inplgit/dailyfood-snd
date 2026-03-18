<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\City;

class DailyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pdfContent;
    public $dateStr;
    public $cityId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pdfContent, $dateStr, $cityId = null)
    {
        $this->pdfContent = $pdfContent;
        $this->dateStr = $dateStr;
        $this->cityId = $cityId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Daily Summary Report - ' . $this->dateStr;
        $filename = 'Daily_Report_Summary_' . $this->dateStr . '.pdf';

        return $this->subject($subject)
                    ->html('<p>Please find the consolidated daily summary report attached for ' . $this->dateStr . '.</p>')
                    ->attachData($this->pdfContent, $filename, [
                        'mime' => 'application/pdf',
                    ]);
    }
}
