<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNameTableTickerType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticker_type', function (Blueprint $table) {
            //rename table ticker_type to ticket_type
            Schema::rename('ticker_type', 'ticket_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticker_type', function (Blueprint $table) {
            //
            Schema::rename('ticket_type', 'ticker_type');
        });
    }
}
