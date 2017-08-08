<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserNameToMembersReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('members_report', function (Blueprint $table) {
            $table->string('user_name')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('members_report', function (Blueprint $table) {
            $table->dropColumn('user_name');
        });
    }
}
