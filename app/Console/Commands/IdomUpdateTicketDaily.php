<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use atomita\Backlog;
use atomita\BacklogException;

/**
 * Update ticket daily from Backlog
 *
 * Sep 26, 201610:25:33 AM
 * @author tampt6722
 *
 */
class IdomUpdateTicketDaily extends CommandIdomBacklog
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idom_update_ticket_daily:get {crawler_type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Ticket info updated daily";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->updateTickets();
        } catch (BacklogException $e) {
            print_r($e->getMessage());
        }
    }

}