<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectKpiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_kpi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->integer('project_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('cost_efficiency')->default(0);
            $table->integer('fix_code')->default(0);
            $table->integer('leakage')->default(0);
            $table->integer('customer_survey')->default(0);
            $table->integer('defect_remove_efficiency')->default(0);
            $table->integer('defect_rate')->default(0);
            $table->integer('uat_bug')->default(0);
            $table->integer('code_productivity')->default(0);
            $table->integer('test_productivity')->default(0);
            $table->integer('actual_cost_efficiency')->default(0);
            $table->integer('plan_cost_efficiency')->default(0);
            $table->integer('actual_fix_code')->default(0);
            $table->integer('plan_fix_code')->default(0);
            $table->integer('actual_leakage')->default(0);
            $table->integer('plan_leakage')->default(0);
            $table->integer('actual_customer_survey')->default(0);
            $table->integer('plan_customer_survey')->default(0);
            $table->integer('actual_defect_remove_efficiency')->default(0);
            $table->integer('plan_defect_remove_efficiency')->default(0);
            $table->integer('actual_defect_rate')->default(0);
            $table->integer('plan_defect_rate')->default(0);
            $table->integer('actual_uat_bug')->default(0);
            $table->integer('plan_uat_bug')->default(0);
            $table->integer('actual_code_productivity')->default(0);
            $table->integer('plan_code_productivity')->default(0);
            $table->integer('actual_test_productivity')->default(0);
            $table->integer('plan_test_productivity')->default(0);
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
