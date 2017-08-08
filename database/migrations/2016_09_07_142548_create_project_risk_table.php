<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectRiskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_risk', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('status', false, true)->default(0);
            $table->smallInteger('impact', false, true)->default(0);
            $table->integer('propability', false, true)->default(0);
            $table->smallInteger('strategy', false, true)->default(0);
            $table->integer('category_id');
            $table->text('mitigration_plan');
            $table->text('risk_title');
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
        Schema::drop('project_risk');
    }
}
