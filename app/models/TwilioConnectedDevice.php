<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class TwilioConnectedDevice extends Model
{
    protected $table = 'twilio_connected_devices';    
    protected $fillable = ['user_id', 'device_id','workers_online'];
    public $timestamps = false;

    public function device($userid){
        return $this->where([
            ['user_id', '=', $userid],
        ])->get();
    }

    public function connectedDevice(){
        return $this->get();
    }

    public function connect($userid,$device_id,$workers_online  ){
        return $this->updateOrCreate(
            ['user_id' => $userid],
            ['device_id' => $device_id, 'workers_online' => $workers_online ]
        );
    }
    public function disconnect($userid){
        return $this->where([
            ['user_id', '=', $userid],
        ])->delete();
    }

}
