<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *
 * Mar 11, 2017 11:21:50 AM
 * @author TamPT6722
 *
 */
class ModifyAssignToEmailToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->renameColumn('assign_to_email', 'assign_to_user');
            $table->renameColumn('created_by_email', 'created_by_user');
            $table->renameColumn('author_email', 'made_by_user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->renameColumn('assign_to_user', 'assign_to_email');
            $table->renameColumn('created_by_user', 'created_by_email');
            $table->renameColumn('made_by_user', 'author_email');
        });
    }
}
