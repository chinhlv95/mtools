<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNameColumTableTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
           //
           $table->renameColumn('integrate_ticker_type_id', 'ticket_type_id');
           $table->dropColumn('integrated_project_id');
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
        Schema::table('tickets', function (Blueprint $table) {
           //
           $table->renameColumn('parent_id', 'integrated_parent_id');
           $table->integer('integrated_project_id');
        });
    }
}
