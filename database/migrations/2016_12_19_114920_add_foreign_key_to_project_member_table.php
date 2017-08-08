<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *
 * Dec 19, 2016 11:51:10 AM
 * @author tampt6722
 *
 */
class AddForeignKeyToProjectMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_member', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->change();
            $table->integer('project_id')->unsigned()->change();
            $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade')->change();
            $table->foreign('project_id')
                    ->references('id')->on('projects')
                    ->onDelete('cascade')->onUpdate('cascade')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_member', function (Blueprint $table) {
            $table->dropForeign('project_member_user_id_foreign');
            $table->dropForeign('project_member_project_id_foreign');
        });
    }
}
