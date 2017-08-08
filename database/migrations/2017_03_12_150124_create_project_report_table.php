<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_report', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('department_id')->default(0);
            $table->integer('project_id')->default(0);
            $table->string('project_name')->default('');
            $table->string('year', 128)->default('');
            $table->integer('month')->default(0);
            $table->integer('tested_tc')->default(0);
            $table->integer('loc')->default(0);
            $table->double('weighted_bug')->default(0);
            $table->double('weighted_uat_bug')->default(0);
            $table->double('actual_hour')->default(0);
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
        Schema::drop('project_report');
    }
}
