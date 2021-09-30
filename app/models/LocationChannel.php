<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocationChannel extends Model
{
	use SoftDeletes;
	
    protected $fillable = ['location_id','channel'];
}
