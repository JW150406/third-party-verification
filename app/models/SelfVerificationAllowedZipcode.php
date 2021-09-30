<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class SelfVerificationAllowedZipcode extends Model
{
    protected $fillable = [
        'zipcode_id'
    ];

    public function zipCode()
    {
        return $this->belongsTo('App\models\Zipcodes','zipcode_id');
    }
}
