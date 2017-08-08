<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrawlerUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crawler_urls', function(Blueprint $table){
            $table->increments('id');
            $table->integer('crawler_type_id');
            $table->string('target_id', 255);
            $table->string('content_type', 255);
            $table->string('last_modified', 32);
            $table->string('etag', 255);
            $table->string('url', 735);
            $table->string('title', 255);
            $table->longText('content');
            $table->dateTime('last_crawled_date');
            $table->dateTime('next_crawled_date');
            $table->tinyInteger('result')->unsigned();
            $table->integer('time_out');
            $table->integer('status_code');
            $table->tinyInteger('errors_count')->unsigned()->default(0);
            $table->string('errors_message', 255)->default('');
            $table->tinyInteger('stop_flg')->unsigned()->default(0);
            $table->tinyInteger('hidden_flg')->unsigned()->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique('target_id');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('crawler_urls');
    }
}
