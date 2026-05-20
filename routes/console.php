<?php


use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Jalankan pengecekan janji temu setiap hari jam 07:00 pagi
Schedule::command('hospital:process-appointments')->dailyAt('07:00');

// Jalankan pembersihan file sampah setiap hari tengah malam
Schedule::command('hospital:clear-deleted-files')->daily();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
