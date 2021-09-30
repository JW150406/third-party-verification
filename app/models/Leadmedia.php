<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leadmedia extends Model
{
    use SoftDeletes;
     
    protected $table = 'leadmedia';

    public function scopeGetSignature($query, $leadId) {
        return $query->where('telesales_id', $leadId)->where('type','image')->latest();
    }

    public function scopeGetAckSignature($query, $leadId) {
        return $query->where('telesales_id', $leadId)->where('type','signature2')->latest();
    }

}
