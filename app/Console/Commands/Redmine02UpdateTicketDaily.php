<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Update Redmine 02 ticket daily
 * Oct 27, 20165:13:01 PM
 * @author tampt6722
 *
 */
class Redmine02UpdateTicketDaily extends CommandRedmine02
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '02_update_ticket_daily:get {crawler_type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Redmine 02 ticket update info';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->updateTicketsDaily();
    }
}
