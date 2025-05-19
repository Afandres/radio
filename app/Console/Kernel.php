<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define los comandos Artisan del sistema.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Define el scheduler de tareas.
     */
    protected function schedule(Schedule $schedule)
    {
        // Aquí se registran tareas programadas
        $schedule->command('radio:programar')->everyMinute();
    }
}
