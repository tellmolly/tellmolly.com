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
        $schedule->command('tm:notify-inactive-users')
            ->dailyAt('13:00')
            ->withoutOverlapping();

        $schedule->command('tm:delete-inactive-users')
            ->dailyAt('14:00')
            ->withoutOverlapping();

        $schedule->command('tm:reset-demo')
            ->hourlyAt(30)
            ->withoutOverlapping();
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
