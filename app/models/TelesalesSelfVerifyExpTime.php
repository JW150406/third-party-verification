<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TelesalesSelfVerifyExpTime extends Model
{
	use SoftDeletes;

   	protected $table = 'telesales_self_verify_exp_time';
   	protected $fillable = [
        'telesale_id', 'verification_mode', 'expire_time'
    ];

    public function telesales()
    {
      return $this->belongsTo('App\models\Telesales', 'telesale_id');
    }
}
