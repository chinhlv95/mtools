<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsReleasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_releases', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id', false, true);
            $table->integer('user_id', false, true);
            //$table->string('redmine_id',45);
            $table->string('name',128)->default('');
            $table->tinyInteger('status')->default(0);
            $table->integer('weighted')->default(0);
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
        Schema::drop('project_releases');
    }
}
