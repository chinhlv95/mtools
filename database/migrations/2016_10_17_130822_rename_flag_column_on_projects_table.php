<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameFlagColumnOnProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('flag');
            $table->tinyInteger('crawler_flag')->after('type_id')->default(0);
            $table->tinyInteger('sync_flag')->after('crawler_flag')->default(0);
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
            $table->tinyInteger('flag')->after('type_id');
            $table->dropColumn('sync_flag');
        });
    }
}
