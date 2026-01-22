<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Scheduled tasks - runs at 3 AM to calculate what needs to be cleaned that day
Schedule::command('hcs:plan-tasks --days=2')
    ->daily()
    ->at('03:00')
    ->timezone('Europe/Amsterdam')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Automatic planning completed successfully');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Automatic planning failed');
    });

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
