<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
class Brandcontacts extends Model
{
    use SoftDeletes;

    protected $table = 'brand_contacts';

    protected $fillable = [
        'name', 'contact','client_id'
    ];
    
    function GetContacts(){
        return $this->orderBy('name','asc')
             ->paginate(20);
    }
    public function savecontact($data){
        return $this->insertGetId($data);
     }
     public function  getBrandContact($id){
        return $this->find($id);
     }
     public function  getBrandContactByName($brandname){
        return $this->select(DB::raw('group_concat(contact) as contacts'))
        ->where('name', $brandname)->get();
     }

     public function  updatecontact($data, $id){ 
        return $this-> where('id',$id)
        ->update( $data );
    }
    public function  deletecontact( $id){ 
        return $this->where('id',$id)
        ->delete();
    }

    public function getBrandsByClient($clientId = '')
    {
        if($clientId != ''){
            return $this->orderBy('name','asc')->where('client_id',$clientId)->get();
        }else{
            return $this->orderBy('name','asc')->get();
        }
    }
     
}
