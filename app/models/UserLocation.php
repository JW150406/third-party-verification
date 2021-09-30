<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserLocation extends Model
{
	use SoftDeletes;
	
    protected $fillable = ['user_id','location_id'];
}
