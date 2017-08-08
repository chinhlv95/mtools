<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->integer('ticket_id')->unsigned();
            $table->integer('user_id')->default(0);
            $table->integer('loc');
            $table->dateTime('integrated_created_at');
            $table->dateTime('integrated_updated_at');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('project_id')
                ->references('id')->on('projects')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('ticket_id')
                ->references('id')->on('tickets')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('locs');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
