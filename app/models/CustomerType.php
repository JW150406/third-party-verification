<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerType extends Model
{
	use SoftDeletes;

    protected $fillable = ['client_id','name'];

    protected $dates = ['deleted_at'];
}
