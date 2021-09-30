<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commodity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id','name', 'status'
    ];

    public function units()
    {
        return $this->hasMany('App\models\CommodityUnit','commodity_id');
    }

    public function forms()
    {
        return $this->belongsToMany('App\models\Clientsforms', 'form_commodities', 'commodity_id', 'form_id');
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function($commodity)
        {
            $commodity->units()->delete();
        });
    }
}
