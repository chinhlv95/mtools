<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnToTableProjectKpi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
    {
        Schema::table('project_kpi', function (Blueprint $table) {
            $table->dropColumn('cost_efficiency');
            $table->dropColumn('fix_code');
            $table->dropColumn('leakage');
            $table->dropColumn('customer_survey');
            $table->dropColumn('defect_remove_efficiency');
            $table->dropColumn('defect_rate');
            $table->dropColumn('uat_bug');
            $table->dropColumn('code_productivity');
            $table->dropColumn('test_productivity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_kpi', function (Blueprint $table) {
             $table->integer('cost_efficiency', false, true)->default(0);
             $table->integer('fix_code', false, true)->default(0);
             $table->integer('leakage', false, true)->default(0);
             $table->integer('customer_survey', false, true)->default(0);
             $table->integer('defect_remove_efficiency', false, true)->default(0);
             $table->integer('defect_rate', false, true)->default(0);
             $table->integer('uat_bug', false, true)->default(0);
             $table->integer('code_productivity', false, true)->default(0);
             $table->integer('test_productivity', false, true)->default(0);
        });
    }
}
