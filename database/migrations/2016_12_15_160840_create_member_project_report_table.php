<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberProjectReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_project_report', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('report_flag');
            $table->integer('user_id');
            $table->text('project_id');
            $table->integer('department_id');
            $table->text('common_data');
            $table->text('quality');
            $table->text('productivity');
            $table->string('time_name', 255);
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
        Schema::drop('member_project_report');
    }
}
