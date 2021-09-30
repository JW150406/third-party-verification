<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class SalescentersBrands extends Model
{
    protected $table = "salescenters_brands";
    protected $fillable = [
      'salescenter_id','brand_id'
    ];

    public function restrictProg()
    {
        return $this->hasMany(SalescenterBrandPrograms::class,'salescenter_brand_id');
    }

    public function getSalescenterBrands($salescenterId)
    {
        return $this->where('salescenter_id',$salescenterId)->get()->toArray();
    }
}
