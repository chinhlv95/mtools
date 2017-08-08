<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('project_id', 45);
            $table->string('name', 45);
            $table->integer('brse', false, true);
            $table->date('plant_start_date')->nullable();
            $table->date('plant_end_date')->nullable();
            $table->tinyInteger('status')->default(0)->unsigned();
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->tinyInteger('type_id')->default(1)->unsigned();
            //$table->string('redmine_id',45)->nullable();
            $table->string('customer_name',128)->default('');
            $table->integer('language_id')->default(0);
            $table->string('process_apply', 45)->default('');
            $table->string('plant_total_effort', 128)->default('');
            $table->string('actual_effort', 128)->default('');
            $table->string('member_assign', 15)->default('');
            $table->text('decreption')->default('');
            $table->integer('resource_need_brse', false, true)->default(0);
            $table->integer('resource_need_dev', false, true)->default(0);
            $table->integer('resource_need_qa', false, true)->default(0);
            $table->integer('user_id', false, true);
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
        Schema::drop('projects');
    }
}
