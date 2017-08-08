<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNameInRootCauseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
    {
        Schema::table('root_cause', function (Blueprint $table) {
            $table->renameColumn('integrated_bug_id', 'integrated_root_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('root_cause', function (Blueprint $table) {
            $table->renameColumn('integrated_root_id', 'integrated_bug_id');
        });
    }
}
