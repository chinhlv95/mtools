<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *
 * Dec 19, 2016 11:46:23 AM
 * @author tampt6722
 *
 */
class AddForeignKeyToEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->integer('ticket_id')->unsigned()->change();
            $table->foreign('ticket_id')
                    ->references('id')->on('tickets')
                    ->onDelete('cascade')->onUpdate('cascade')->change();
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
            $table->dropForeign('entries_ticket_id_foreign');
        });
    }
}
