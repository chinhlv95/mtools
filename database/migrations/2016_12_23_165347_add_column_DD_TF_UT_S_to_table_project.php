<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDDTFUTSToTableProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('detail_design')->default(0)->after('sync_flag');
            $table->integer('test_first')->default(0)->after('detail_design');
            $table->integer('unit_test')->default(0)->after('test_first');
            $table->integer('scenario')->default(0)->after('unit_test');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('detail_design');
            $table->dropColumn('test_first');
            $table->dropColumn('unit_test');
            $table->dropColumn('scenario');
        });
    }
}
