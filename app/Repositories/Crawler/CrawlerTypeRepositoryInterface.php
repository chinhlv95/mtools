<?php
namespace App\Repositories\Crawler;
interface CrawlerTypeRepositoryInterface
{
    public function all();

    public function paginate($quantity);

    public function find($id);

    public function findByAttribute($att,$name);

    public function save($data);

    public function delete($id);

    public function update($data, $id);
}