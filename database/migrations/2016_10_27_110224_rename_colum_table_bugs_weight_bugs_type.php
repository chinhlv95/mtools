<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumTableBugsWeightBugsType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bugs_weight', function (Blueprint $table) {
            $table->renameColumn('integrated_bug_id', 'integrated_bug_weight_id');
        });
        Schema::table('bugs_type', function (Blueprint $table) {
            $table->renameColumn('integrated_bug_id', 'integrated_bug_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bugs_weight', function (Blueprint $table) {
            $table->renameColumn('integrated_bug_weight_id', 'integrated_bug_id');
        });
        Schema::table('bugs_type', function (Blueprint $table) {
            $table->renameColumn('integrated_bug_type_id', 'integrated_bug_id');
        });
    }
}
