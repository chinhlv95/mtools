<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *
 * Dec 20, 2016 2:25:14 PM
 * @author tampt6722
 *
 */
class AddPositionToMemberProjectReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_project_report', function (Blueprint $table) {
            $table->string('position', 255)->after('user_id');
            $table->integer('project_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_project_report', function (Blueprint $table) {
            $table->dropColumn('position');
            $table->text('project_id');
        });
    }
}
