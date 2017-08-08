<?php
namespace App\Repositories\Crawler;

use App\Models\CrawlerUrl;
use Carbon\Carbon;
use Illuminate\Http\Response;
use App\Models\Ticket;

class CrawlerUrlRepository implements CrawlerUrlRepositoryInterface
{
    public function all(){
        return CrawlerUrl::all();
    }

    public function paginate($quantity){
        return CrawlerUrl::paginate($quantity);
    }

    public function find($id){
        return CrawlerUrl::find($id);
    }

    public function findFromCrawlerTypeId($id, $quantity){
        return CrawlerUrl::where('crawler_type', $id)
            ->paginate($quantity);
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Crawler\CrawlerUrlRepositoryInterface::save()
     */
    public function save($data){
        $crawlerUrl = new CrawlerUrl();
            // crawler url save data
        $crawlerUrl->crawler_type_id = $data['crawler_type_id'];
        $crawlerUrl->target_id = $data['target_id'];
        $crawlerUrl->content_type = $data['content_type'];
        $crawlerUrl->last_modified = $data['last_modified'];
        $crawlerUrl->etag = $data['etag'];
        $crawlerUrl->title = $data['title'];
        $crawlerUrl->url = $data['url'];
        $crawlerUrl->content = $data['content'];
        $crawlerUrl->result = $data['result'];
        $crawlerUrl->time_out = $data['time_out'];
        $crawlerUrl->last_crawled_date = $data['last_crawled_date'];
        $crawlerUrl->next_crawled_date = $data['next_crawled_date'];
        $crawlerUrl->status_code = $data['status_code'];
        $crawlerUrl->errors_count = $data['errors_count'];
        $crawlerUrl->errors_message = $data['errors_message'];
        $crawlerUrl->stop_flg = $data['stop_flg'];
        $crawlerUrl->hidden_flg = $data['hidden_flg'];
        $crawlerUrl->save();
        return $crawlerUrl->id;
    }

    public function delete($id){
        CrawlerUrl::find($id)->delete();
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Crawler\CrawlerUrlRepositoryInterface::update()
     */
    public function update($data, $id){
        $crawlerUrl = CrawlerUrl::find($id);
        if (!empty($data['title'])) {
            $crawlerUrl->title = $data['title'];
        }
        $crawlerUrl->content = $data['content'];
        $crawlerUrl->result = $data['result'];
        $crawlerUrl->last_crawled_date = $data['last_crawled_date'];
        $crawlerUrl->next_crawled_date = $data['next_crawled_date'];
        $crawlerUrl->save();
        return true;
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Crawler\CrawlerUrlRepositoryInterface::updateWithError()
     */
    public function updateWithError($data, $id){
        $crawlerUrl = CrawlerUrl::find($id);
        $crawlerUrl->status_code = $data['status_code'];
        $crawlerUrl->errors_count = $data['errors_count'];
        $crawlerUrl->errors_message =$data['errors_message'];
        $crawlerUrl->save();
        return true;
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Crawler\CrawlerUrlRepositoryInterface::getProjectNeedUpdate()
     */
    public function getProjectNeedUpdate($dateNow, $url, $crawlerTypeId){
        $query = CrawlerUrl::select('projects.id as projectId','projects.project_id',
                            'projects.name','projects.project_key', 'crawler_urls.id as crawler_url_id',
                            'crawler_urls.errors_count',
                            'crawler_types.name as crawler_types_name')
                    ->join('projects', function($join) {
                        $join->on('crawler_urls.target_id', '=', 'projects.id')
                        ->where('projects.sync_flag', '=', 1)
                        ->where('projects.crawler_flag', '>', 0);
                    })
                    ->join('crawler_types', function($join) use($crawlerTypeId) {
                        $join->on('crawler_urls.crawler_type_id', '=', 'crawler_types.id')
                        ->where('crawler_types.id' , '=', $crawlerTypeId);
                    })
                    ->where('crawler_urls.url','=', $url)
                    ->where('crawler_urls.stop_flg','=', 0)
                    ->where('crawler_urls.hidden_flg','=', 0)
                    ->where('crawler_urls.next_crawled_date','<=', $dateNow)
                    ->orderBy('projects.created_at','DESC')->get();
        return $query;
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Crawler\CrawlerUrlRepositoryInterface::findByAttribute()
     */
    public function findByAttribute($att, $name){
        return CrawlerUrl::where($att, $name)->first();
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Crawler\CrawlerUrlRepositoryInterface::findByAttributes()
     */
    public function findByAttributes($att1, $name1, $att2, $name2){
        return CrawlerUrl::where($att1, $name1)
                            ->where($att2,$name2)
                            ->first();
    }

    /**
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Crawler\CrawlerUrlRepositoryInterface::getTicketNeedUpdate()
     */
    public function getTicketNeedUpdate($dateNow, $url, $crawlerTypeId){
        $query = Ticket::select('crawler_urls.id as crawler_urls_id',
                'crawler_urls.errors_count',
                'projects.project_id as integrated_project_id',
                'tickets.*')
        ->join('crawler_urls','crawler_urls.target_id', '=', 'tickets.id')
        ->join('crawler_types', function($join) use($crawlerTypeId) {
            $join->on('crawler_urls.crawler_type_id', '=', 'crawler_types.id')
            ->where('crawler_types.id' , '=', $crawlerTypeId);
        })
        ->join('projects', function($join) {
            $join->on('projects.id', '=', 'tickets.project_id')
            ->where('projects.sync_flag', '=', 1)
            ->where('projects.crawler_flag', '=', 2);
        })
        ->where('crawler_urls.url', '=', $url)
        ->where('crawler_urls.stop_flg', '=', 0)
        ->where('crawler_urls.hidden_flg', '=', 0)
        ->where('crawler_urls.next_crawled_date', '<=', $dateNow)
        ->orderBy('tickets.created_at', 'DESC')->get();
        return $query;
    }

    public function findCrawUrlByAttributes($att1, $name1, $att2, $name2,$att3, $name3 ){
        return CrawlerUrl::where($att1, $name1)
                            ->where($att2,$name2)
                            ->where($att3, $name3)
                            ->first();
    }

    /**
     * update crawler url of a ticket
     *
     * @author tampt6722
     * {@inheritDoc}
     * @see \App\Repositories\Crawler\CrawlerUrlRepositoryInterface::updateCrawlerUrl()
     */
    public function updateCrawlerUrl($crawlerUrlId, $ticket, $today) {
        $crawlerUrl['content'] = serialize($ticket);
        $crawlerUrl['result'] = true;
        $crawlerUrl['last_crawled_date'] = $today;
        $crawlerUrl['next_crawled_date'] = Carbon::now()->addDay()->toDateTimeString();
        $this->update($crawlerUrl, $crawlerUrlId);
    }


    /**
     * Save crawler url data
     * @author tampt6722
     *
     * @param CrawlerTypeRepository $crawlerType
     * @param integer $ticketId
     * @param array $ticket
     * @return void
     */
    public function saveToCrawlerUrls($crawlerTypeId, $ticketId, $ticket, $content, $url){
        $checkCrawlerUrl = $this->findCrawUrlByAttributes('crawler_type_id', $crawlerTypeId,
                'target_id', $ticketId, 'url', $url);
        if (count($checkCrawlerUrl) == 0) {
            $crawlerUrl ['crawler_type_id'] = $crawlerTypeId;
            $crawlerUrl ['target_id'] = $ticketId;
            $crawlerUrl ['content_type'] = $content;
            $crawlerUrl ['last_modified'] = "";
            $crawlerUrl ['etag'] = "";
            $crawlerUrl ['title'] = "";
            $crawlerUrl ['url'] = $url;
            $crawlerUrl ['content'] = serialize($ticket);
            $crawlerUrl ['result'] = true;
            $crawlerUrl ['time_out'] = 0;
            $crawlerUrl ['last_crawled_date'] = Carbon::now ()->toDateTimeString ();
            $crawlerUrl ['next_crawled_date'] = Carbon::now ()->addDay ()->toDateTimeString ();
            $crawlerUrl ['status_code'] = app('Illuminate\Http\Response')->status ();
            $crawlerUrl ['errors_count'] = 0;
            $crawlerUrl ['errors_message'] = "Not error";
            $crawlerUrl ['stop_flg'] = 0;
            $crawlerUrl ['hidden_flg'] = 0;
            $this->save($crawlerUrl );
        }
    }
}