<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Artisan::command('schedule:run', function (Schedule $schedule) {
    $schedule->command('reports:send-scheduled-emails')
        ->dailyAt('13:00'); // Ajusta la hora según lo necesario
})->describe('Programar el envío de correos programados de los reportes.');
