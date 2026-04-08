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
    public $ccEmails;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pdfContent, $dateStr, $cityId = null, $ccEmails = [])
    {
        $this->pdfContent = $pdfContent;
        $this->dateStr = $dateStr;
        $this->cityId = $cityId;
        $this->ccEmails = $ccEmails;
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

        $mail = $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject($subject)
                    ->view('emails.daily_report')
                    ->with([
                        'dateStr' => $this->dateStr,
                    ])
                    ->attachData($this->pdfContent, $filename, [
                        'mime' => 'application/pdf',
                    ]);

        if (!empty($this->ccEmails)) {
            $mail->cc($this->ccEmails);
        }

        return $mail;
    }
}
