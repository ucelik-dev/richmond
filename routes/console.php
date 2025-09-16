<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('app:send-student-installment-reminders')
    ->dailyAt('01:19')   // pick your time
    ->timezone('Europe/London'); // optionally set explicit TZ