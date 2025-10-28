<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $number;
    protected $text;

    public function __construct($number , $text)
    {
        $this->number = $number;
        $this->text = $text;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $url = env('BIZSMS_API_URL');
        $params = [
            'username' => env('BIZSMS_USERNAME'),
            'pass' => env('BIZSMS_PASSWORD'),
            'text' => $this->text,
            'masking' => env('BIZSMS_MASKING'),
            'destinationnum' => $this->number,
            'language' => 'English',
            'responsetype' => 'text'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
        }
        curl_close($ch);

        if (isset($error_msg)) {
            return $error_msg;
        }
        Log::channel('queue-worker')->info("SMS sent to {$this->number}: $response");
        // return $response;
    }
}
