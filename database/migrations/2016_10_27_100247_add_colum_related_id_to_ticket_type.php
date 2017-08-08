<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumRelatedIdToTicketType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_type', function (Blueprint $table) {
            $table->integer('related_id')->default(0)->after('source_id');
        });
        Schema::table('status', function (Blueprint $table) {
            $table->integer('related_id')->default(0)->after('source_id');
        });
        Schema::table('activities', function (Blueprint $table) {
            $table->integer('related_id')->default(0)->after('source_id');
        });
        Schema::table('project_versions', function (Blueprint $table) {
            $table->integer('related_id')->default(0)->after('source_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_type', function (Blueprint $table) {
            $table->dropColumn('related_id');
        });
        Schema::table('status', function (Blueprint $table) {
            $table->dropColumn('related_id');
        });
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('related_id');
        });
        Schema::table('project_versions', function (Blueprint $table) {
            $table->dropColumn('related_id');
        });
    }
}
