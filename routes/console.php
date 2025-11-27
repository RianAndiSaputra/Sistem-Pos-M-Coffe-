<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
// use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('forecast:generate --type=all')
    ->dailyAt('01:00')
    ->withoutOverlapping()
    ->onOneServer() // Jika pakai multiple servers
    ->appendOutputTo(storage_path('logs/forecast.log'));    