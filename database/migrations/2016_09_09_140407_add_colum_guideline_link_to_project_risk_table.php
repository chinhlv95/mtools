<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumGuidelineLinkToProjectRiskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_risk', function (Blueprint $table) {
            //add colum guideline_link
            $table->string('guideline_link')->after('risk_title');
            $table->string('task_id')->after('guideline_link');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_risk', function (Blueprint $table) {
            //
            $table->dropColumn('guideline_link');
            $table->dropColumn('task_id');
        });
    }
}
