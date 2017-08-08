<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnToPriorityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('priority', function (Blueprint $table) {
            $table->renameColumn('integrated_bug_id', 'integrated_priority_id');
            $table->integer('source_id')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('priority', function (Blueprint $table) {
            $table->renameColumn('integrated_priority_id', 'integrated_bug_id');
            $table->integer('source_id');
        });
    }
}
