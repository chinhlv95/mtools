<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteRowOfKpi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_kpi', function (Blueprint $table) {
            $table->dropColumn('actual_loc');
            $table->dropColumn('actual_UAT_bug_number');
            $table->dropColumn('actual_bug_number');
            $table->dropColumn('actual_UAT_bug_weighted');
            $table->dropColumn('actual_bug_weighted');
            $table->dropColumn('actual_fix_bug_cost');
            $table->dropColumn('estimate_total_cost');
            $table->dropColumn('actual_total_cost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_kpi', function (Blueprint $table) {
            $table->double('actual_loc', 8, 2);
            $table->double('actual_UAT_bug_number', 8, 2);
            $table->double('actual_bug_number', 8, 2);
            $table->double('actual_UAT_bug_weighted', 8, 2);
            $table->double('actual_bug_weighted', 8, 2);
            $table->double('actual_fix_bug_cost', 8, 2);
            $table->double('estimate_total_cost', 8, 2);
            $table->double('actual_total_cost', 8, 2);
        });
    }
}
