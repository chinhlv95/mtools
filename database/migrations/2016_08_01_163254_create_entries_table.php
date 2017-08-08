<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('integrated_ticket_id');
            $table->integer('integrated_project_id');
            $table->integer('integrated_parent_id');
            $table->integer('source_id');
            $table->integer('actual_hour');
            $table->integer('integrated_activity_id');
            $table->dateTime('spent_at');
            $table->integer('year');
            $table->integer('month');
            $table->integer('week');
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
        Schema::drop('entries');
    }
}
