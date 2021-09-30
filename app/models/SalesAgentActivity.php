<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class SalesAgentActivity extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
    	'agent_id',
    	'in_time',
    	'out_time',
    	'total_time',
    	'activity_type',
    	'start_lat',
    	'start_lng',
    	'end_lat',
    	'end_lng'
    ];

    protected $casts = [
        'start_lat' => 'double',
        'start_lng' => 'double',
        'end_lat' => 'double',
        'end_lng' => 'double',

    ];

    public function agent() {
        return $this->belongsTo('App\User', 'agent_id');
    }
    
    protected static function boot()
    {
    	parent::boot();

    	static::updating(function($model) {
    		if ($model->isDirty('out_time'))
    		{
    			$model->total_time = $model->out_time->diffInSeconds($model->in_time);
    		}
    	});
    }
}
