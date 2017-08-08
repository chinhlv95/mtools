<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaskToProjectReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_report', function (Blueprint $table) {
            $table->string('department_name')->after('department_id')->default('');
            $table->integer('task')->after('loc')->default(0);
            $table->integer('status')->after('loc')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_report', function (Blueprint $table) {
            $table->dropColumn('task');
            $table->dropColumn('status');
            $table->dropColumn('department_name');
        });
    }
}
