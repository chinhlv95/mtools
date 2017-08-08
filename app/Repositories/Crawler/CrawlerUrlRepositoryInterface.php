<?php
namespace App\Repositories\Crawler;
interface CrawlerUrlRepositoryInterface
{
    public function all();

    public function paginate($quantity);

    public function find($id);

    public function findFromCrawlerTypeId($id, $quantity);

    public function save($data);

    public function delete($id);

    public function update($data, $id);

    public function updateWithError($data, $id);

    public function getProjectNeedUpdate($dateNow, $url, $crawlerTypeId);

    public function findByAttribute($att, $name);

    public function findByAttributes($att1, $name1, $att2, $name2);

    public function getTicketNeedUpdate($dateNow, $url, $crawlerTypeId);

    public function findCrawUrlByAttributes($att1, $name1, $att2, $name2,$att3, $name3 );

    public function updateCrawlerUrl($crawlerUrlId, $ticket, $today);

    public function saveToCrawlerUrls($crawlerTypeId, $ticketId, $ticket, $content, $url);
}