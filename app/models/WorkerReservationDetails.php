<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class WorkerReservationDetails extends Model
{
    protected $fillable = [
        'reservation_id',
        'task_id',
        'worker_id',
        'reservation_created_time',
        'reservation_status',
        'call_hung_up_by'
    ];

    protected $primarykey = 'id';
    protected $table = 'twilio_worker_reservation_details';
}
