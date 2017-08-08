<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumTeamAndFileTypeToTableFileImport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_import', function (Blueprint $table) {
            $table->integer('team')->after('user_id')->nullable();
            $table->string('file_type',25)->after('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('file_import', function (Blueprint $table) {
            //
            $table->dropColumn('team');
            $table->dropColumn('file_type');
        });
    }
}
