<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TelesalesdataTmp extends Model
{
  use SoftDeletes;
  
  protected $fillable = [
      'telesaletmp_id', 'meta_key', 'meta_value', 'field_id'
  ];

  protected $primarykey = 'id';
  protected $table = 'telesalesdata_tmp';
  public $timestamps = false;

  public function teleSalesData()
  {
      return $this->belongsTo('App\models\TelesalesTmp', 'telesaletmp_id');
  }

  public function formFieldsData()
  {
      return $this->belongsTo('App\models\FormField', 'field_id');
  }

}
