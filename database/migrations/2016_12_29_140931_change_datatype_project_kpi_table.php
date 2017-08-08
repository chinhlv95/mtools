<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDatatypeProjectKpiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_kpi', function (Blueprint $table) {
            $table->float('actual_cost_efficiency',8,2)->change();
            $table->float('plan_cost_efficiency',8,2)->change();
            $table->float('actual_fix_code',8,2)->change();
            $table->float('plan_fix_code',8,2)->change();
            $table->float('actual_leakage',8,2)->change();
            $table->float('plan_leakage',8,2)->change();
            $table->float('actual_customer_survey',8,2)->change();
            $table->float('plan_customer_survey',8,2)->change();
            $table->float('actual_defect_remove_efficiency',8,2)->change();
            $table->float('plan_defect_remove_efficiency',8,2)->change();
            $table->float('actual_defect_rate',8,2)->change();
            $table->float('plan_defect_rate',8,2)->change();
            $table->float('actual_uat_bug',8,2)->change();
            $table->float('plan_uat_bug',8,2)->change();
            $table->float('actual_code_productivity',8,2)->change();
            $table->float('plan_code_productivity',8,2)->change();
            $table->float('actual_test_productivity',8,2)->change();
            $table->float('plan_test_productivity',8,2)->change();
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

        });
    }
}
