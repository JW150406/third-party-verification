<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class SettingTPVnowRestrictedTimeZone extends Model
{
    protected $table = 'settings_tpv_now_restricted_timezones';

    protected $fillable = [
		'client_id',
        'state',
        'start_time',
        'end_time',
        'timezone',
    ];
}
