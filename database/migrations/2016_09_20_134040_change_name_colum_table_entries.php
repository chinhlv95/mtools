<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNameColumTableEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entries', function (Blueprint $table) {
           //
           $table->renameColumn('integrated_ticket_id', 'ticket_id');
           $table->renameColumn('integrated_project_id', 'project_id');
           $table->renameColumn('integrated_parent_id', 'parent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entries', function (Blueprint $table) {
            //
           $table->renameColumn('ticket_id', 'integrate_ticker_id');
           $table->renameColumn('project_id', 'integrated_project_id');
           $table->renameColumn('parent_id', 'integrated_parent_id');
        });
    }
}
