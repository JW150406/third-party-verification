<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class UtilityMapping extends Model
{
    protected $fillable =['utility_id', 'mapped_utility_id'];
    public $timestamps = false;
}
