<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salescenterslocations extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name', 'street', 'city','state','country','zip','client_id','created_by','created_at','updated_at', 'salescenter_id','code','contact_name','contact_number', 'status'
    ];

    protected $primarykey = 'id';
    protected $table = 'salescenterslocations';

    public function channels() {
        return $this->hasMany(LocationChannel::class,'location_id');
    }

    public function salescenter() {
        return $this->belongsTo(Salescenter::class,'salescenter_id');
    }

    public function getLocations($client_id,$salescenter_id){
        return $this->where([
             ['client_id', '=', $client_id],                       
             ['salescenter_id', '=', $salescenter_id]                      
           ])->orderBy('id','DESC')->paginate(20);
     }
     
     public function createLocation($data){
        return $this->insertGetId(
            [ 
                'name' => $data['name'], 
                'street' => $data['street'],
                'city' => $data['city'],
                'state' => $data['state'],
                'country' => $data['country'],
                'zip' => $data['zip'],
                'created_by' => $data['created_by'],
                'salescenter_id' => $data['salescenter_id'],
                'client_id' => $data['client_id'],
                'code' => $data['code'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                ]
        );
     }
    public function getLocationDetail($location_id)
    {
        return $this->where([
            ['id', '=', $location_id]                       
          ])->firstOrFail();
    }
    public function updateLocation($id,$data)
    {
        $update_Array = array(
            'name' => $data['name'], 
            'street' => $data['street'],
            'city' => $data['city'],
            'state' => $data['state'],
            'country' => $data['country'],
            'zip' => $data['zip'],
        );
        return $this -> where('id',$id)
                  ->update( $update_Array  );
    } 
    public function getLocationsInfo($client_id,$salescenter_id)
    {
        return $this->select('id','name')->where([
            ['client_id', '=', $client_id],                       
            ['salescenter_id', '=', $salescenter_id]                       
          ])->orderBy('name','asc')->get();
    }
    public function getclientLocationsInfo($client_id)
    {
        return $this->select('id','name')->where([
            ['client_id', '=', $client_id]               
          ])->where('status','active')->orderBy('name','asc')->get();
    }
    public function getLocationsList( $client_id = null )
    {
        return $this->select('id','name')->distinct()
        ->when($client_id, function ($query, $client_id) {
            return $query->where('client_id', $client_id);
        })
        ->orderBy('name','asc')->get();
    }

    public function getSingleLocationInfo($location_id)
    {
        return $this->where([
            ['id', '=', $location_id]
          ])->get();
    }
    public function getSaleslocationCode($id)
    {
        $code =  $this->select('code')->where([
            ['id', $id]                                
        ])->first();
        return array_get($code, 'code');
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function($location)
        {
            $location->channels()->delete();
        });
    }
    
}
