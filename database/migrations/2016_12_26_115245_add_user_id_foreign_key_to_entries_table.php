<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *
 * Dec 26, 2016 11:55:46 AM
 * @author tampt6722
 *
 */
class AddUserIdForeignKeyToEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->integer('project_id')->unsigned()->change();
            $table->integer('user_id')->unsigned()->change();
            $table->foreign('project_id')
                    ->references('id')->on('projects')
                    ->onDelete('cascade')->onUpdate('cascade')->change();
            $table->foreign('user_id')
                    ->references('id')->on('users')
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
        Schema::table('entries', function (Blueprint $table) {
           $table->dropForeign('entries_project_id_foreign');
           $table->dropForeign('entries_user_id_foreign');
        });
    }
}
