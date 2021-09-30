<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class LeadmediaTemp extends Model
{
    protected $fillable = ['telesales_tmp_id', 'name', 'type', 'url', 'expire', 'ip_address'];
}
