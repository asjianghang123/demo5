<?php

namespace App\Console;

use App\Jobs\SiteCheck;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    use DispatchesJobs;
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
        // Commands\test::class,
        Commands\SiteCheck::class,

        Commands\WilliamTableUpdate::class,

        Commands\Do_ChkSiteMain::class,
        Commands\Do_chkLatLonDir_main::class,
        Commands\GetAbnormalStationCounts::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {   
        date_default_timezone_set('PRC');
        // $schedule->command('sitecheck:check')->dailyAt("00:01");
        $schedule->command('sitecheck:check')->dailyAt("01:01");
        // $schedule->command('sitecheck:check')->everyMinute();

        // $schedule->command('SiteCheck')->everyMinute();
        // $schedule->job(new SiteCheck)->everyFiveMinutes();
        // $schedule->call(function () {
        //     $this->dispatch(new SiteCheck());
        // })->dailyAt('00:01');

        $schedule->command('williamTableUpdate')->dailyAt('04:00');
        // $schedule->command('Do_ChkSiteMain')->dailyAt('04:30');
        // $schedule->command('Do_chkLatLonDir_main')->dailyAt('04:30');
        // $schedule->command('GetAbnormalStationCounts')->dailyAt('05:10');
    }
}
