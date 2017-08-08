<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('project_id');
            $table->date('crawled_date');
            $table->integer('status_code');
            $table->string('errors_message', 255)->default('');
            $table->bigInteger('ticket_count')->default(0);
            $table->integer('crawler_type_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('log_projects');
    }
}
