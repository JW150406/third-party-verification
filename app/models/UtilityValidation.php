<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class UtilityValidation extends Model
{
    protected $fillable =['client_id', 'utility_id', 'label','regex','regex_message'];
    public $timestamps = false;
}
