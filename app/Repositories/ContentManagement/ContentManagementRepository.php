<?php
namespace App\Repositories\ContentManagement;

use App\Models\Activity;
use App\Models\BugType;
use App\Models\BugWeight;
use App\Models\Priority;
use App\Models\RootCause;
use App\Models\Status;
use App\Models\TicketType;

class ContentManagementRepository implements ContentManagementRepositoryInterface{

    public function findBySourceAndType($type,$source,$attributes){
        $query = '';
        switch ($type) {
            case 1:
                    $query = Status::select($attributes);
                    foreach($source as $key => $value){
                        $query->where($key,'=',$value);
                    }
                break;

            case 2:
                    $query = TicketType::select($attributes);
                    foreach($source as $key => $value){
                        $query->where($key,'=',$value);
                    }
                break;

            case 3:
                    $query = Priority::select($attributes);
                    foreach($source as $key => $value){
                        $query->where($key,'=',$value);
                    }
                break;
            case 4:
                    $query = BugWeight::select($attributes);
                    foreach($source as $key => $value){
                        $query->where($key,'=',$value);
                    }
                break;
            case 5:
                    $query = BugType::select($attributes);
                    foreach($source as $key => $value){
                        $query->where($key,'=',$value);
                    }
                break;
            case 6:
                    $query = RootCause::select($attributes);
                    foreach($source as $key => $value){
                        $query->where($key,'=',$value);
                    }
                break;
            case 7:
                    $query = Activity::select($attributes);
                    foreach($source as $key => $value){
                        $query->where($key,'=',$value);
                    }
                break;
            default:
                    $query = '';
                    return $query;
                ;
            break;
        }
        return $query->get();
    }

    public function checkExits($type,$name){
        $query = '';
        switch ($type) {
            case 1:
                $query = Status::select('name','id')->where('name','=',$name);
                break;

            case 2:
                $query = TicketType::select('name','id')->where('name','=',$name);
                break;

            case 3:
                $query = Priority::select('name','id')->where('name','=',$name);
                break;
            case 4:
                $query = BugWeight::select('name','id')->where('name','=',$name);
                break;
            case 5:
                $query = BugType::select('name','id')->where('name','=',$name);
                break;
            case 6:
                $query = RootCause::select('name','id')->where('name','=',$name);
                break;
            case 7:
                $query = RootCause::select('name','id')->where('name','=',$name);
                break;
            default:
                $query = '';
                ;
                break;
        }
        return $query->first();
    }

    public function save($type,$name, $source_id){
        $table = '';

        switch ($type) {
            case 1:
                    $last_row = Status::select('key','related_id')->orderBy('key','desc')->first()->toArray();
                    $last_key = $last_row['key'];
                    $last_related_id = $last_row['related_id'];

                    $table = new Status;
                    $table->key = $last_key + 1;
                    $table->name = $name;
                    $table->source_id = $source_id;
                    $table->related_id = $last_related_id + 1;
                break;

            case 2:
                    $last_row = TicketType::select('key','related_id')->orderBy('key','desc')->first()->toArray();
                    $last_key = $last_row['key'];
                    $last_related_id = $last_row['related_id'];

                    $table = new TicketType;
                    $table->key = $last_key + 1;
                    $table->name = $name;
                    $table->source_id = $source_id;
                    $table->related_id = $last_related_id + 1;
                break;

            case 3:
                    $last_row = Priority::select('key','related_id')->orderBy('key','desc')->first()->toArray();
                    $last_key = $last_row['key'];
                    $last_related_id = $last_row['related_id'];

                    $table = new Priority;
                    $table->key = $last_key + 1;
                    $table->name = $name;
                    $table->source_id = $source_id;
                    $table->related_id = $last_related_id + 1;
                break;
            case 4:
                    $last_row = BugWeight::select('key','related_id')->orderBy('key','desc')->first()->toArray();
                    $last_key = $last_row['key'];
                    $last_related_id = $last_row['related_id'];

                    $table = new BugWeight;
                    $table->key = $last_key + 1;
                    $table->name = $name;
                    $table->source_id = $source_id;
                    $table->related_id = $last_related_id + 1;
                break;
            case 5:
                    $last_row = BugType::select('key','related_id')->orderBy('key','desc')->first()->toArray();
                    $last_key = $last_row['key'];
                    $last_related_id = $last_row['related_id'];

                    $table = new BugType;
                    $table->key = $last_key + 1;
                    $table->name = $name;
                    $table->source_id = $source_id;
                    $table->related_id = $last_related_id + 1;
                break;
            case 6:
                    $last_row = RootCause::select('key','related_id')->orderBy('key','desc')->first()->toArray();
                    $last_key = $last_row['key'];
                    $last_related_id = $last_row['related_id'];

                    $table = new RootCause;
                    $table->key = $last_key + 1;
                    $table->name = $name;
                    $table->source_id = $source_id;
                    $table->related_id = $last_related_id + 1;
                break;
            case 7:
                    $last_row = Activity::select('key','related_id')->orderBy('key','desc')->first()->toArray();
                    $last_key = $last_row['key'];
                    $last_related_id = $last_row['related_id'];

                    $table = new Activity;
                    $table->key = $last_key + 1;
                    $table->name = $name;
                    $table->source_id = $source_id;
                    $table->related_id = $last_related_id + 1;
                break;
            default:
                    $table = '';
                break;
        }

        $table->save();
        $last_insert_data = [$table->key,$table->source_id];
        return $last_insert_data;
    }

    public function update($type,$attributes,$id){
        switch ($type) {
            case 1:
                $query = Status::where('id',$id);
                break;

            case 2:
                $query = TicketType::where('id',$id);
                break;

            case 3:
                $query = Priority::where('id',$id);
                break;
            case 4:
                $query = BugWeight::where('id',$id);
                break;
            case 5:
                $query = BugType::where('id',$id);
                break;
            case 6:
                $query = RootCause::where('id',$id);
                break;
            case 7:
                $query = Activity::where('id',$id);
                break;
            default:
                $query = '';
                ;
                break;
        }
        return $query->update($attributes);
    }

    public function all($type){
         switch ($type) {
            case 1:
                $query = Status::all('name','key');
                break;

            case 2:
                $query = TicketType::all('name','key');
                break;

            case 3:
                $query = Priority::all('name','key');
                break;
            case 4:
                $query = BugWeight::all('name','key');
                break;
            case 5:
                $query = BugType::all('name','key');
                break;
            case 6:
                $query = RootCause::all('name','key');
                break;
            case 7:
                $query = Activity::all('name','key');
                break;
            default:
                $query = '';
                ;
                break;
        }
        return $query;
    }
}
