<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIntegratedTicketIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropForeign('entries_ticket_id_foreign');
            $table->dropForeign('entries_user_id_foreign');
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
            $table->integer('ticket_id')->unsigned()->change();
            $table->foreign('ticket_id')
                ->references('id')->on('tickets')
                ->onDelete('cascade')->onUpdate('cascade')->change();
            $table->integer('user_id')->unsigned()->change();
                $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade')->change();
        });
    }
}
