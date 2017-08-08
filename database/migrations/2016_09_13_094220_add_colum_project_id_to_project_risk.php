<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumProjectIdToProjectRisk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_risk', function (Blueprint $table) {
            $table->integer('project_id')->after('id');
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
            $table->dropColumn('project_id');
        });
    }
}
