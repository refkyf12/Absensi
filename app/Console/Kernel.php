<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\SoapController;
use App\Models\liburNasional;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $liburNasionalDates = LiburNasional::pluck('tanggal')->toArray();
        dd(1);

        $schedule->call('App\Http\Controllers\SoapController@logAbsenStore')
            ->timezone('Asia/Jakarta')
            ->weeklyOn(2, '15:51')
            ->skip(function ($date) use ($liburNasionalDates){
                return in_array($date->format('Y-m-d'), $liburNasionalDates);
            });
        
        $schedule->call('App\Http\Controllers\SoapController@logAbsenStore')
            ->timezone('Asia/Jakarta')
            ->weeklyOn(3, '01:00')
            ->skip(function ($date) use ($liburNasionalDates){
                return in_array($date->format('Y-m-d'), $liburNasionalDates);
            });

        $schedule->call('App\Http\Controllers\SoapController@logAbsenStore')
            ->timezone('Asia/Jakarta')
            ->weeklyOn(4, '01:00')
            ->skip(function ($date) use ($liburNasionalDates){
                return in_array($date->format('Y-m-d'), $liburNasionalDates);
            });

        $schedule->call('App\Http\Controllers\SoapController@logAbsenStore')
            ->timezone('Asia/Jakarta')
            ->weeklyOn(5, '01:00')
            ->skip(function ($date) use ($liburNasionalDates){
                return in_array($date->format('Y-m-d'), $liburNasionalDates);
            });

        $schedule->call('App\Http\Controllers\SoapController@logAbsenStore')
            ->timezone('Asia/Jakarta')
            ->weeklyOn(6, '01:00')
            ->skip(function ($date) use ($liburNasionalDates){
                return in_array($date->format('Y-m-d'), $liburNasionalDates);
            });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
