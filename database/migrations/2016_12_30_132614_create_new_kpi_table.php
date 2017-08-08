<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewKpiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('project_kpi');
        Schema::create('project_kpi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->integer('project_id');
            $table->integer('baseline_flag');
            $table->date('start_date');
            $table->date('end_date');
            $table->float('actual_cost_efficiency',8,2)->nullable();
            $table->float('plan_cost_efficiency',8,2)->nullable();
            $table->float('actual_fix_code',8,2)->nullable();
            $table->float('plan_fix_code',8,2)->nullable();
            $table->float('actual_leakage',8,2)->nullable();
            $table->float('plan_leakage',8,2)->nullable();
            $table->float('actual_customer_survey',8,2)->nullable();
            $table->float('plan_customer_survey',8,2)->nullable();
            $table->float('actual_bug_after_release_number',8,2)->nullable();
            $table->float('plan_bug_after_release_number',8,2)->nullable();
            $table->float('actual_bug_after_release_weight',8,2)->nullable();
            $table->float('plan_bug_after_release_weight',8,2)->nullable();
            $table->float('actual_defect_remove_efficiency',8,2)->nullable();
            $table->float('plan_defect_remove_efficiency',8,2)->nullable();
            $table->float('actual_defect_rate',8,2)->nullable();
            $table->float('plan_defect_rate',8,2)->nullable();
            $table->float('actual_code_productivity',8,2)->nullable();
            $table->float('plan_code_productivity',8,2)->nullable();
            $table->float('actual_test_case_productivity',8,2)->nullable();
            $table->float('plan_test_case_productivity',8,2)->nullable();
            $table->float('actual_tested_productivity',8,2)->nullable();
            $table->float('plan_tested_productivity',8,2)->nullable();
            $table->text('description')->default('');
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
        Schema::drop('project_kpi');
    }
}
