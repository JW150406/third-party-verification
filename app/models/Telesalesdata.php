<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Telesalesdata extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'telesale_id', 'meta_key', 'meta_value', 'field_id'
    ];

    protected $primarykey = 'id';
    protected $table = 'telesalesdata';
    protected $dates = ['deleted_at'];
    public $timestamps = false;

    public function createLeadDetail($data)
    {
       return $this->insert(
           [
               'telesale_id' => $data['telesale_id'],
               'meta_key' => $data['meta_key'],
               'meta_value' => $data['meta_value'],
               ]
       );
    }
    public function leadDetail($telesale_id)
    {

       return $this->where([
             ['telesale_id', '=', $telesale_id],
         ])->get();
    }
    public function UpdateDetail($lead_id, $meta_key, $meta_value )
    {
       return $this->where('telesale_id', $lead_id)
                   ->where('meta_key', $meta_key)
                   ->update(['meta_value' => $meta_value]);
    }

    public function leadMetakeyData($telesale_id,$meta_key)
    {

       $data = $this->select('meta_value')->where([
             ['telesale_id', '=', $telesale_id],
             ['meta_key', '=', $meta_key],
         ])->first();
         if($data){
            return $data->meta_value;
         }else{
            return "";
         }
    }

    public function teleSalesData()
    {
        return $this->belongsTo('App\models\Telesales', 'telesale_id');
    }

    public function formFieldsData()
    {
        return $this->belongsTo('App\models\FormField', 'field_id');
    }

    public function scopeFieldVal($query, $leadId, $fieldId = "", $type = "value") {
        $result = $query->where('telesale_id', $leadId)->where('meta_key', $type);

        if ($fieldId != "") {
            $result = $query->where('field_id', $fieldId);
        }

        return $result;
    }
}
