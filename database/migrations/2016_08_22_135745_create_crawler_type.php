<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateCrawlerType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crawler_types', function(Blueprint $table){
            $table->increments('id');
            $table->string('name', 255);
            $table->dateTime('last_crawled_date')->nullable();
            $table->bigInteger('crawl_interval')->default(0);
            $table->bigInteger('fetch_interval')->default(0);
            $table->integer('time_out', false, true)->default(84000);
            $table->integer('stream_type', false, true);
            $table->integer('fetcher_type')->default(-1);
            $table->tinyInteger('stop_flg', false, true)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('crawler_types');
    }
}
