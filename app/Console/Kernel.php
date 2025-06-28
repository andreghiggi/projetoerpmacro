<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->command('alerta:cron')->dailyAt(1,13);
        $schedule->command('alerta:cron')->everyMinute();
        $schedule->command('cash-back:cron')->dailyAt('08:00');
        $schedule->command('app:boleto-cron')->monthly();
        $schedule->command('app:sync-orders-conecta-venda')->everyFifteenMinutes();
        $schedule->command('app:sync-estoque')->everyTenMinutes();
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
