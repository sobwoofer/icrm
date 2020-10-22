<?php

namespace App\Console;

use App\Console\Commands\CrawlVendors;
use App\Console\Commands\SyncProducts;
use App\Console\Commands\TelegramCheckUpdates;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CrawlVendors::class,
        SyncProducts::class,
        TelegramCheckUpdates::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('crawl-vendors')->daily()->at('01:00');
        $schedule->command('sync-products')->daily()->at('04:00');
        $schedule->command('telegram-check-updates')->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
