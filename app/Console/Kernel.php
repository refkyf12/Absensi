<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\SoapController;
use App\Http\Controllers\AbsenNonKerjaController;
use App\Http\Controllers\KetidakhadiranController;
use App\Http\Controllers\LogActivityController;

use App\Models\liburNasional;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $liburNasionalDates = LiburNasional::pluck('tanggal')->toArray();
        $tanggal  = date('Y-m-d',strtotime("-1 days"));

        $hari = date('w', strtotime($tanggal));

        if ($hari === "0"||$hari === "6"){
            $schedule->call('App\Http\Controllers\AbsenNonKerjaController@logAbsenNonKerja')
                ->timezone('Asia/Jakarta')
                ->dailyAt('01:00');
            $schedule->call('App\Http\Controllers\LogActivityController@store')
                ->timezone('Asia/Jakarta')
                ->dailyAt('01:04');
        } else {
            if (in_array($tanggal, $liburNasionalDates)){
                $schedule->call('App\Http\Controllers\AbsenNonKerjaController@logAbsenNonKerja')
                    ->timezone('Asia/Jakarta')
                    ->dailyAt('01:00');
                $schedule->call('App\Http\Controllers\LogActivityController@store')
                    ->timezone('Asia/Jakarta')
                    ->dailyAt('01:04');

            }else{
                $schedule->call('App\Http\Controllers\SoapController@logAbsenStore')
                    ->timezone('Asia/Jakarta')
                    ->dailyAt('01:00');

                $schedule->call('App\Http\Controllers\KetidakhadiranController@store')
                    ->timezone('Asia/Jakarta')
                    ->dailyAt('01:02');

                $schedule->call('App\Http\Controllers\LogActivityController@store')
                    ->timezone('Asia/Jakarta')
                    ->dailyAt('01:04');
            }
        }

        // $schedule->call('App\Http\Controllers\SoapController@logAbsenStore')
        //     ->timezone('Asia/Jakarta')
        //     ->weeklyOn(2, '15:51')
        //     ->skip(function ($date) use ($liburNasionalDates){
        //         return in_array($date->format('Y-m-d'), $liburNasionalDates);
        //     });
        
        // $schedule->call('App\Http\Controllers\SoapController@logAbsenStore')
        //     ->timezone('Asia/Jakarta')
        //     ->weeklyOn(3, '01:00')
        //     ->skip(function ($date) use ($liburNasionalDates){
        //         return in_array($date->format('Y-m-d'), $liburNasionalDates);
        //     });

        // $schedule->call('App\Http\Controllers\SoapController@logAbsenStore')
        //     ->timezone('Asia/Jakarta')
        //     ->weeklyOn(4, '01:00')
        //     ->skip(function ($date) use ($liburNasionalDates){
        //         return in_array($date->format('Y-m-d'), $liburNasionalDates);
        //     });

        // $schedule->call('App\Http\Controllers\SoapController@logAbsenStore')
        //     ->timezone('Asia/Jakarta')
        //     ->weeklyOn(5, '01:00')
        //     ->skip(function ($date) use ($liburNasionalDates){
        //         return in_array($date->format('Y-m-d'), $liburNasionalDates);
        //     });

        // $schedule->call('App\Http\Controllers\SoapController@logAbsenStore')
        //     ->timezone('Asia/Jakarta')
        //     ->weeklyOn(6, '01:00')
        //     ->skip(function ($date) use ($liburNasionalDates){
        //         return in_array($date->format('Y-m-d'), $liburNasionalDates);
        //     });
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
