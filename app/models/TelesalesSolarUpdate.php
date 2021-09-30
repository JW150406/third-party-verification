<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use DB;

class TelesalesSolarUpdate extends Model
{
    protected $table = "telesales_solar_update";

    public function getUpdates($id)
    {
          $response = $this->where([ ['telesales_id', '=', $id]])->get();
          return $response;
    }
}
