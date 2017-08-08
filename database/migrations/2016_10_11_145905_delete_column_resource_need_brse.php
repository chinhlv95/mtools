<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteColumnResourceNeedBrse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('resource_need_brse');
            $table->dropColumn('resource_need_dev');
            $table->dropColumn('resource_need_qa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
             $table->integer('resource_need_brse', false, true)->default(0);
             $table->integer('resource_need_dev', false, true)->default(0);
             $table->integer('resource_need_qa', false, true)->default(0);
        });
    }
}
