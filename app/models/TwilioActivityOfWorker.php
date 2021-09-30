<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class TwilioActivityOfWorker extends Model
{
    protected $table = 'twilio_activity_of_workers';

    protected $fillable = [
        'worker_id', 
        'worker_activity_id', 
        'worker_activity_name',
    ];
}
