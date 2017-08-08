<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Update CoWell tickets daily
 *
 * Oct 14, 2016 10:07:34 AM
 * @author tampt6722
 *
 */
class CwUpdateTicketDaily extends CommandCwRedmine
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cw_update_ticket_daily:get {crawler_type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'cw ticket update info';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->updateTicketsDaily();
    }


}
