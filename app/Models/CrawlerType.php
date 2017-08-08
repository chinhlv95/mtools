<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrawlerType extends Model
{
    use SoftDeletes;
    protected $table = 'crawler_types';
    protected $fillable=[
        'name',
        'last_crawled_date',
        'crawl_interval',
        'fetch_interval',
        'time_out',
        'stream_type',
        'fetcher_type',
        'process_urls',
        'stop_flg',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $dates = ['deleted_at'];


    /**
     * Get the crawler urls for the crawler type.
     */
    public function crawlerUrls()
    {
        return $this->hasMany('App\Models\CrawlerUrl');
    }
}
