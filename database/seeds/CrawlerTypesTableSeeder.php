<?php

use Illuminate\Database\Seeder;
use App\Models\CrawlerType;

class CrawlerTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // truncate data of crawler_typs
        DB::table('crawler_types')->truncate();

        // init data
        $data = [
            [
                'name' => 'idom_backlog',
                'crawl_interval' => 86400,
                'fetch_interval' => -1,
                'time_out' => 0,
                'stream_type' => '1',
                'fetcher_type' => '1',
                'stop_flg' => 0,
            ],
            [
                'name' => 'gdo_redmine',
                'crawl_interval' => 86400,
                'fetch_interval' => -1,
                'time_out' => 0,
                'stream_type' => '2',
                'fetcher_type' => '1',
                'stop_flg' => 0,
            ],
            [
                'name' => 'redmine_02',
                'crawl_interval' => 86400,
                'fetch_interval' => -1,
                'time_out' => 0,
                'stream_type' => '3',
                'fetcher_type' => '1',
                'stop_flg' => 0,
            ],
            [
                'name' => 'cowell_redmine',
                'crawl_interval' => 86400,
                'fetch_interval' => -1,
                'time_out' => 0,
                'stream_type' => '4',
                'fetcher_type' => '1',
                'stop_flg' => 0,
            ],
        ];

        // create database data
        foreach ($data as $item){
            CrawlerType::create($item);
        }
    }
}
