<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *
 * Nov 1, 201610:42:36 AM
 * @author tampt
 *
 */
class RenameAndAddColumsToTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->renameColumn('integrated_ticket_type_id', 'ticket_type_id');
            $table->text('impact_analysis')->after('bug_type_id')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->renameColumn('ticket_type_id', 'integrated_ticket_type_id');
            $table->dropColumn('impact_analysis');
        });
    }
}
