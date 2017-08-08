<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIntegratedUpdatedAtToEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dateTime('integrated_created_at')->after('spent_at');
            $table->dateTime('integrated_updated_at')->after('integrated_created_at');
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
            $table->dropColumn('integrated_created_at');
            $table->dropColumn('integrated_updated_at');
        });
    }
}
