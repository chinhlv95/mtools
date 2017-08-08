<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrawlerUrl extends Model
{
    use SoftDeletes;
    protected $table = 'crawler_urls';
    protected $fillable=[
        'crawler_type_id',
        'target_id',
        'content_type',
        'last_modified',
        'etag',
        'url',
        'title',
        'content',
        'last_crawled_date',
        'next_crawled_date',
        'result',
        'time_out',
        'status_code',
        'errors_count',
        'errors_message',
        'stop_flg',
        'hidden_flg',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $dates = ['deleted_at'];

    /**
     * Get the crawler type that owns the crawler url.
     */
    public function crawlerType()
    {
        return $this->belongsTo('App\Models\CrawlerType', 'crawler_type_id', 'id');
    }

    public function tickets()
    {
         return $this->belongsTo('App\Models\Ticket', 'target_id','id');
    }

    public function projects()
    {
        return $this->hasOne('App\Models\Project','target_id','id');
    }
}
