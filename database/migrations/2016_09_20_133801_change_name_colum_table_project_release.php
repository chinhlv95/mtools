<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNameColumTableProjectRelease extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_releases', function (Blueprint $table) {
           //rename colum user_id to ticket_id
           $table->renameColumn('user_id', 'ticket_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_releases', function (Blueprint $table) {
            //
            $table->renameColumn('ticket_id', 'user_id');
        });
    }
}
