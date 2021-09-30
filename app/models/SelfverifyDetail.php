<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SelfverifyDetail extends Model
{
	use SoftDeletes;
	
    protected $fillable = [
        'telesale_id','ip','platform_name','plaform_model','os','os_version','browser','browser_version','user_latitude','user_longitude','gps_location_image'
    ];
}
