<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Update GDO tickets daily
 *
 * Oct 14, 201610:07:34 AM
 * @author tampt6722
 *
 */
class GdoUpdateTicketDaily extends CommandGDORedmine {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gdo_update_ticket_daily:get {crawler_type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gdo ticket update info';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->updateTicketsDaily();
    }
}
