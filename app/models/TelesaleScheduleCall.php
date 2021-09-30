<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\Telesales;
class TelesaleScheduleCall extends Model
{

    protected $fillable = [
        'telesale_id', 'call_immediately', 'call_time','call_lang', 'call_type', 'attempt_no', 'disposition', 'dial_status', 'task_id', 'schedule_status'
    ];

    protected $primarykey = 'id';
    protected $table = 'telesale_schedule_call';
    public $timestamps = true;

    public function updateValue($data,$id)
    {
        $this::where('id', $id)
            ->update($data);
    }
    public function Telesale() {
        return $this->belongsTo(Telesales::class, 'telesale_id');
    }
}
