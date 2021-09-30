<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class TwilioCurrentActivityOfWorker extends Model
{
    protected $table = 'twilio_current_activity_of_workers';

    protected $fillable = [
        'worker_id', 
        'worker_activity_id', 
        'worker_activity_name',
    ];
}
