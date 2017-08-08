<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *
 * Nov 5, 20163:07:27 PM
 * @author tampt6722
 *
 */
class ChangeTypeTargetIdFieldOnCrawlerUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crawler_urls', function (Blueprint $table) {
            $table->integer('target_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crawler_urls', function (Blueprint $table) {
            $table->string('target_id', 255);
        });
    }
}
