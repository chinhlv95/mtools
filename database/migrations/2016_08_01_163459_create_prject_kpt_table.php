<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrjectKptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_kpt', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id', false, true);
            $table->integer('user_id', false, true);
            $table->integer('category_id');
            $table->tinyInteger('status')->default(0);
            //$table->string('value',45);
            $table->text('content');
            $table->text('action');
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
        Schema::drop('project_kpt');
    }
}
