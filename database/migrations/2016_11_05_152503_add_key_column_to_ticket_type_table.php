<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *
 * Nov 5, 20163:27:25 PM
 * @author tampt6722
 *
 */
class AddKeyColumnToTicketTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_type', function (Blueprint $table) {
            $table->integer('key')->after('id')->default(0);
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
            $table->dropColumn('key');
        });
    }
}
