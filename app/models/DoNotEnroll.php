<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class DoNotEnroll extends Model
{
    protected $fillable = ['client_id', 'account_number'];

    public function getClientWiseList($clientId)
    {
        return $this->where('client_id',$clientId)->get();
    }
}
