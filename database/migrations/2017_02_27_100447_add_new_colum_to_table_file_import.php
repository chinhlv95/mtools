<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumToTableFileImport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_import', function (Blueprint $table) {
            $table->integer('user_id')->after('name')->nullable();
            $table->smallInteger('status')->after('name')->nullable();
            $table->integer('project_id')->after('name')->nullable();
            $table->string('type',25)->after('name')->nullable();
            $table->integer('parent_id')->after('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('file_import', function (Blueprint $table) {
            //
            $table->dropColumn('user_id');
            $table->dropColumn('status');
            $table->dropColumn('type');
            $table->dropColumn('project_id');
            $table->dropColumn('parent_id');
        });
    }
}
