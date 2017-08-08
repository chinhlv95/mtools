<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *
 * Dec 19, 2016 11:46:35 AM
 * @author tampt6722
 *
 */
class AddForeignKeyToTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->integer('ticket_type_id')->unsigned()->change();
            $table->integer('status_id')->unsigned()->change();
            $table->integer('project_id')->unsigned()->change();
            $table->integer('bug_weight_id')->unsigned()->change();
            $table->integer('bug_type_id')->unsigned()->change();
            $table->integer('priority_id')->unsigned()->change();
            $table->integer('root_cause_id')->unsigned()->change();

            $table->foreign('ticket_type_id')
                    ->references('id')->on('ticket_type')
                    ->onDelete('cascade')->onUpdate('cascade')->change();
            $table->foreign('status_id')
                    ->references('id')->on('status')
                    ->onDelete('cascade')->onUpdate('cascade')->change();
            $table->foreign('project_id')
                    ->references('id')->on('projects')
                    ->onDelete('cascade')->onUpdate('cascade')->change();
            $table->foreign('bug_weight_id')
                    ->references('id')->on('bugs_weight')
                    ->onDelete('cascade')->onUpdate('cascade')->change();
            $table->foreign('bug_type_id')
                    ->references('id')->on('bugs_type')
                    ->onDelete('cascade')->onUpdate('cascade')->change();
            $table->foreign('priority_id')
                    ->references('id')->on('priority')
                    ->onDelete('cascade')->onUpdate('cascade')->change();
            $table->foreign('root_cause_id')
                    ->references('id')->on('root_cause')
                    ->onDelete('cascade')->onUpdate('cascade')->change();
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
           $table->dropForeign('tickets_ticket_type_id_foreign');
           $table->dropForeign('tickets_status_id_foreign');
           $table->dropForeign('tickets_project_id_foreign');
           $table->dropForeign('tickets_bug_weight_id_foreign');
           $table->dropForeign('tickets_bug_type_id_foreign');
           $table->dropForeign('tickets_priority_id_foreign');
           $table->dropForeign('tickets_root_cause_id_foreign');

        });
    }
}
