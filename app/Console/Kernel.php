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
        // Commands\Inspire::class,
        Commands\GetUserPortal::class,
        Commands\IdomGetProjectInfo::class,
        Commands\IdomGetTicketInfo::class,
        Commands\IdomUpdateProjectFirstly::class,
        Commands\IdomUpdateProjectDaily::class,
        Commands\IdomUpdateTicketDaily::class,
        Commands\IdomUpdateTicketNew::class,
        Commands\CwGetProjectInfo::class,
        Commands\CwGetTicketInfo::class,
        Commands\CwUpdateProjectDaily::class,
        Commands\CwUpdateProjectFirstly::class,
        Commands\CwUpdateTicketNew::class,
        Commands\CwUpdateTicketDaily::class,
        Commands\Redmine02GetProjectInfo::class,
        Commands\Redmine02GetTicketInfo::class,
        Commands\Redmine02UpdateProjectFirstly::class,
        Commands\Redmine02UpdateProjectDaily::class,
        Commands\Redmine02UpdateTicketNew::class,
        Commands\Redmine02UpdateTicketDaily::class,
        Commands\GdoGetProjectInfo::class,
        Commands\GdoGetTicketInfo::class,
        Commands\GdoUpdateProjectFirstly::class,
        Commands\GdoUpdateProjectDaily::class,
        Commands\GdoUpdateTicketNew::class,
        Commands\GdoUpdateTicketDaily::class,
        Commands\GetDepartment::class,
        Commands\GetDataMemberForQP::class,
        Commands\GetDataProjectForQP::class,
        Commands\DailyKpiStatistical::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /** Idom Backlog **/
       $schedule->command('idom_update_project_firstly:get idom_backlog')
                ->dailyAt('00:00');
       $schedule->command('idom_update_project_daily:get idom_backlog')
                 ->dailyAt('01:00');
       $schedule->command('idom_update_new_ticket:get idom_backlog')
                ->dailyAt('02:00');
       $schedule->command('idom_update_ticket_daily:get idom_backlog')
                ->dailyAt('02:00');

        /** Redmine 02 **/
        $schedule->command('02_update_project_firstly:get redmine_02')
            ->dailyAt('00:00');
        $schedule->command('02_update_project_daily:get redmine_02')
            ->dailyAt('01:00');
//         $schedule->command('02_ticket_info:get redmine_02')
//         ->dailyAt('00:00');
        $schedule->command('02_update_new_ticket:get redmine_02')
            ->dailyAt('02:00');
        $schedule->command('02_update_ticket_daily:get redmine_02')
            ->dailyAt('02:00');

        /** Cowell Redmine  **/
        $schedule->command('cw_update_project_firstly:get cowell_redmine')
            ->dailyAt('00:00');
        $schedule->command('cw_update_project_daily:get cowell_redmine')
            ->dailyAt('01:00');
        $schedule->command('cw_update_new_ticket:get cowell_redmine')
             ->dailyAt('02:00');
        $schedule->command('cw_update_ticket_daily:get cowell_redmine')
             ->dailyAt('02:00');
    }
}
