<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTickerTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticker_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('source_id', false, true)->default(0);
            $table->integer('integrate_ticker_type_id', false, true)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ticker_type');
    }
}
