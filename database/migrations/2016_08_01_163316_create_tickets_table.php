<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('integrated_ticket_id');
            $table->integer('integrated_project_id');
            $table->integer('integrated_parent_id');
            $table->integer('source_id');
            $table->integer('integrate_ticker_type_id');
            $table->integer('status_id');
            $table->string('title',128);
            $table->string('category',128);
            $table->string('version',128);
            $table->integer('estimate_time');
            $table->dateTime('start_date');
            $table->dateTime('due_date');
            $table->integer('progress');
            $table->dateTime('completed_date');
            $table->string('created_by_email',128);
            $table->string('assign_to_email',128);
            $table->string('author_email',128);
            $table->integer('integrated_bug_type_id');
            $table->integer('loc');
            $table->integer('test_case');
            $table->integer('project_id', false, true);
            $table->tinyInteger('status', false, true)->default(0);
            $table->integer('user_id', false, true);
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
        Schema::drop('tickets');
    }
}
