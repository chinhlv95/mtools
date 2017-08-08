<?php
namespace App;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
trait Updater {

    protected static function boot() {

        static::creating(function($model) {
            $model->created_by = Sentinel::getUser()->id;
            $model->updated_by = Sentinel::getUser()->id;
            parent::boot();
        });

        static::updating(function($model)  {
            $model->updated_by = Sentinel::getUser()->id;
        });

        static::deleting(function($model)  {
            $model->deleted_by = Sentinel::getUser()->id;
            $model->save();
        });
    }
}