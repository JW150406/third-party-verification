<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class UserDocuments extends Model
{
    protected $table = 'user_documents';

     protected $fillable = ['user_id','path','name','uploaded_by'];

    public function addDocument($userid,$filepath,$filename,$uploadedby){
        return $this->insertGetId(
            [
                'user_id' => $userid,
                'path' => $filepath,
                'name' => $filename,
                'uploaded_by' =>  $uploadedby,
                'created_at' =>  date('Y-m-d H:i:s'),
           ]
        );

    }
    public function getUserDocuments($userid){
        return $this->where('user_id',$userid)->orderBy('id','DESC')->paginate(20);
    }

    public function deletefile($id){
        return  $this->where('id', '=',$id )->delete();

    }



}
