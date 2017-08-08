<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *
 * Sep 21, 20164:43:03 PM
 * @author tampt6722
 *
 */
class CreateProjectVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('integrated_version_id');
            $table->integer('project_id');
            $table->string('name',255);
            $table->text('description');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('project_versions');
    }
}
