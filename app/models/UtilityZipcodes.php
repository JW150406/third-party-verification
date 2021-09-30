<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class UtilityZipcodes extends Model
{
    //
    protected $primarykey = 'id';
    protected $table = 'utility_zipcodes';
    public $timestamps = false;
    protected $fillable =['utility_id', 'zipcode_id'];

    public function getUtilityZipcodes($utility_id){
        return  $this->select('zip_codes.*')->join('zip_codes', 'zip_codes.id', '=', 'utility_zipcodes.zipcode_id')
          ->where('utility_zipcodes.utility_id', '=', $utility_id)->get();
    }

    public function zipCode()
    {
        return $this->belongsTo('App\models\Zipcodes','zipcode_id');
    }
}
