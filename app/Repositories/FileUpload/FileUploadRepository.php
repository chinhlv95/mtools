<?php
namespace App\Repositories\FileUpload;

use App\Models\FileUpload;

class FileUploadRepository implements fileUploadRepositoryInterface
{
    public function findFileUploadByProjectId($project_id){
        $fileUpload = FileUpload::where('project_id',$project_id)
                        ->orderBy('created_at', 'desc')
                        ->get();
        return $fileUpload;
    }

    public function find($id){
        return FileUpload::find($id);
    }

    public function save($name,$size,$extentions,$project_id){
        $fileUpload = new FileUpload();
        $fileUpload->name = $name;
        $fileUpload->size = $size;
        $fileUpload->extension = $extentions;
        $fileUpload->project_id = $project_id;
        $fileUpload->save();
        return $fileUpload->id;
    }

    public function delete($id){
        FileUpload::find($id)->delete();
    }
}