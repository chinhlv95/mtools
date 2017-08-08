<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteRowInEntryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropColumn('project_id');
            $table->dropColumn('source_id');
            $table->dropColumn('year');
            $table->dropColumn('month');
            $table->dropColumn('week');
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
            $table->integer('project_id', false, true);
            $table->integer('source_id', false, true);
            $table->integer('year', false, true);
            $table->integer('month', false, true);
            $table->integer('week', false, true);
        });
    }
}
