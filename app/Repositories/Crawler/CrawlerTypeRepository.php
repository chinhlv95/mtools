<?php
namespace App\Repositories\Crawler;
use App\Models\CrawlerType;

class CrawlerTypeRepository implements CrawlerTypeRepositoryInterface
{
    public function all(){
        return CrawlerType::all();
    }

    public function paginate($quantity){
        return CrawlerType::paginate($quantity);
    }

    public function find($id){
        return CrawlerType::find($id);
    }

    public function findByAttribute($att, $name){
        return CrawlerType::where($att, $name)->first();
    }

    public function save($data){
        $crawlerType = new CrawlerType();
        // create new crawler type
        $crawlerType->save($data);

        return $crawlerType->id;
    }

    public function delete($id){
        CrawlerType::find($id)->delete();
    }

    public function update($data, $id){
        $crawlerType = CrawlerType::find($id);
        $crawlerType->name = $data['name'];
        $crawlerType->save();
    }
}