<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *
 * Sep 21, 20164:44:00 PM
 * @author tampt6722
 *
 */
class ChangeNameColumnTicketTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_type', function (Blueprint $table) {
            $table->renameColumn('integrate_ticker_type_id', 'integrated_ticket_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_type', function (Blueprint $table) {
            $table->renameColumn('integrated_ticket_type_id', 'integrate_ticker_type_id');
        });
    }
}
