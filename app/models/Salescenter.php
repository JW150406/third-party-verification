<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salescenter extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'street', 'city','state','country','zip','client_id','created_by','status','logo','contact'
    ];

    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * for use local scope query
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('status','active');
    }
    /**
     * for check client is active or not
     * @return bool
     */
    public  function isActiveClient()
    {
        return isset($this->client) && $this->client->status == 'active';
    }

    /**
     * for check sales center is active or not
     * @return bool
     */
    public  function isActive()
    {
        return $this->status == 'active';
    }

    public function client() {
        return $this->belongsTo("App\models\Client",'client_id');
    }
    public function getSalesCentersList()
    {
        return $this->select('id','name','client_id')->where([
            ['status', '=', 'active']                                
          ])->orderBy('name','asc')->get();
    }
    public function getSalesCentersListByClientID($client_id)
    {
        return $this->select('id','name','status')->where([
            ['status', '=', 'active'] ,
            ['client_id', '=', $client_id ]                               
          ])->orderBy('name','asc')->get();
    }
    public function getSalescenterCode($id)
   {
      $code =  $this->select('code')->where([
        ['id', '=', $id]                                
      ])->first();
      return  array_get($code, 'code');
   }
   public function getSalescenterinfo($id)
   {
    $info =  $this->where([
        ['id', '=', $id]                                
      ])->get();
      if(isset($info[0])){
         $Data = $info[0];
      }else{
        $Data = array();
      }
      return  $Data;
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function($salesCenter)
        {
            Salescenterslocations::where('salescenter_id',$salesCenter->id)->delete();
        });
    }

    public function location() {
        return $this->hasOne("App\models\Salescenterslocations",'salescenter_id');
   }
}
