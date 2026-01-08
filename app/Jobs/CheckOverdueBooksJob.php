<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CheckOverdueBooksJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Run the artisan command
        Artisan::call('books:check-overdue', [
            '--days' => 1,
        ]);

        $output = Artisan::output();

        Log::info('CheckOverdueBooksJob executed', [
            'output' => $output,
        ]);
    }
}
