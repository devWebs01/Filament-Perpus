<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule: Check overdue books daily at 9 AM
Schedule::command('books:check-overdue')
    ->dailyAt('09:00')
    ->description('Check for overdue books and send notifications');
