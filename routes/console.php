<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Scheduled tasks
Schedule::command('hcs:plan-tasks --days=2')
    ->daily()
    ->at('00:00')
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
