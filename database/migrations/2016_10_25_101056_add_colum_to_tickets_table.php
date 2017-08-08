<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumToTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->integer('bug_weight_id')->after('project_id');
            $table->integer('priority_id')->after('bug_weight_id');
            $table->integer('bug_type_id')->after('priority_id');
            $table->dropColumn('integrated_bug_type_id');
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
            $table->dropColumn('bug_weight_id');
            $table->dropColumn('priority_id');
            $table->dropColumn('bug_type_id');
        });
    }
}
