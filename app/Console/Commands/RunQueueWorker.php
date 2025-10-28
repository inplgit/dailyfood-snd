<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunQueueWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:run-worker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the queue worker';

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
    public function handle()
    {
        $this->info('Starting the queue worker...');
        \Log::channel('queue-worker')->info('Starting the queue worker.');
        // Execute the queue:work command
        \Artisan::call('queue:work', [
            '--stop-when-empty' => true,
        ]);

        $this->info('Queue worker has started.');
        \Log::channel('queue-worker')->info('Queue worker started.');

        // return 0;
    }
}
