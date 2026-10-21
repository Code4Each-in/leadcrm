<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Close attendance left open by sessions that expired without the user
// ever coming back, so their Time Out is their last activity.
Schedule::call(fn () => app(\App\Services\AttendanceService::class)->closeIdle())
    ->everyFiveMinutes()
    ->name('attendance:close-idle')
    ->withoutOverlapping();
