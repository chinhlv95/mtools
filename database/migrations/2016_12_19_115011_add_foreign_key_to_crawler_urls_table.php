<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *
 * Dec 19, 2016 11:51:22 AM
 * @author tampt6722
 *
 */
class AddForeignKeyToCrawlerUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crawler_urls', function (Blueprint $table) {
                $table->integer('crawler_type_id')->unsigned()->change();
                $table->foreign('crawler_type_id')
                        ->references('id')->on('crawler_types')
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
        Schema::table('crawler_urls', function (Blueprint $table) {
            $table->dropForeign('crawler_urls_crawler_type_id_foreign');
        });
    }
}
