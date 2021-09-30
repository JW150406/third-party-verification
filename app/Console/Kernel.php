<?php

namespace App\Console;

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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('leads:followup')->everyFiveMinutes();
        $schedule->command('leads:cancel')->everyFiveMinutes();
        $schedule->command('activity:clockout')->everyFiveMinutes();
        $schedule->command('activity:clear')->monthly();
        $schedule->command('reschedule:tasks')->everyMinute();
        // $schedule->command('tpv-recordings:download')->everyFiveMinutes();
        $schedule->command('agent:offline')->everyFifteenMinutes();
        $schedule->command('twilio:api')->everyFifteenMinutes();
        $schedule->command('twilio-previous-date:api')->dailyAt('0:05')->timezone(getClientSpecificTimeZone());
        $schedule->command('makereport:rrhenrollment')->dailyAt('0:01')->timezone(getClientSpecificTimeZone()); 
        $schedule->command('sendEnrollmentReportMega:verified')->dailyAt('6:00')->timezone('America/Toronto');
        $schedule->command('sendreport:leautomated')->dailyAt('6:30')->timezone('America/Toronto');
        $schedule->command('makereport:boltenegry')->dailyAt('0.01')->timezone(getClientSpecificTimeZone());
    }

    /**
     * Get the timezone that should be used by default for scheduled events.
     *
     * @return \DateTimeZone|string|null
     */
    protected function scheduleTimezone()
    {
        return 'America/Toronto';
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
