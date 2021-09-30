<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class TwilioLeadCallDetails extends Model
{
    protected $fillable = [
        'lead_id',
        'call_id',
        'client_id',
        'task_id',
        'worker_id',
        'call_type',
        'call_duration',
        'disposition_id',
        'previous_status',
        'lead_status',
        'recording_url',
        'twilio_recording_url',
        'task_created_time',
        'task_wrapup_start_time',
        'task_completed_time'
    ];

    protected $primarykey = 'id';
    protected $table = 'twilio_lead_call_details';

    public function telesales()
    {
        return $this->belongsTo('App\models\Telesales','lead_id');
    }

}

