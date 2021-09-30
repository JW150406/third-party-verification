<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class SalescenterBrandPrograms extends Model
{
    protected $fillable = ['salescenter_brand_id', 'program_id'];
    public $timestamps = false;
}
