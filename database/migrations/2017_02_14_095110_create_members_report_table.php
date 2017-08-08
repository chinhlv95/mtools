<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members_report', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('department_id')->default(0);
            $table->integer('project_id')->default(0);
            $table->integer('user_id')->default(0);
            $table->string('email', 128)->default('');
            $table->string('name', 255)->default('');
            $table->string('position', 128)->default('');
            $table->string('year', 128)->default('');
            $table->integer('month')->default(0);
            $table->double('workload')->default(0);
            $table->integer('task')->default(0);
            $table->double('kloc')->default(0);
            $table->double('bug_weighted')->default(0);
            $table->double('madebug_weighted')->default(0);
            $table->double('foundbug_weighted')->default(0);
            $table->integer('testcase_create')->default(0);
            $table->integer('testcase_test')->default(0);
            $table->double('test_workload')->default(0);
            $table->double('createTc_workload')->default(0);
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
        Schema::drop('members_report');
    }
}
