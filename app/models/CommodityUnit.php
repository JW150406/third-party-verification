<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommodityUnit extends Model
{
	use SoftDeletes;



    protected $fillable = ['commodity_id','unit'];

	public $timestamps = false;
    protected $dates = ['deleted_at'];
}
