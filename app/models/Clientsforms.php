<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clientsforms extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'created_by', 'client_id', 'description', 'channel', 'created_at','updated_at','commodity_type', 'workflow_id', 'workspace_id', 'formname', 'status','multienrollment'
    ];

    protected $primarykey = 'id';
    protected $table = 'clientsforms';
    protected $dates = ['deleted_at'];

    protected $casts = [
      'created_at' => 'datetime:Y-m-d &\nb\sp;&\nb\sp;&\nb\sp; H:i',
    ];

    public function getClientFormFields($formid)
    {

       return $this->where([
             ['id', '=', $formid],
         ])->get();
    }
    public function getClientFormDetail($formid)
    {

       return $this->where([
             ['id', '=', $formid],
         ])->first();
    }
    public function getClientFormByCommodityType($commodity_type, $client_id)
    {

       return $this->where([
             ['commodity_type', '=', $commodity_type],
             ['client_id', '=', $client_id],
         ])->get();
    }

    public function getClientFields($client_id)
    {

       return $this->where([
             ['client_id', '=', $client_id],
         ])->paginate(1);
    }

    public function getClientForms($client_id)
    {

       return $this->where([
             ['client_id', '=', $client_id],
         ])->paginate(20);
    }
    public function getAllFormUsingClientID($client_id)
    {

       return $this->select('id','formname')
             ->where([
             ['client_id', '=', $client_id],
         ])->get();
    }
    public function getAllFormUsingClientIDandUtilityID($client_id,$utility_id)
    {

       return $this->select('id','formname')
             ->where([
             ['client_id', '=', $client_id],
             ['utility_id', '=', $utility_id],
         ])->get();
    }
    public function createForm($data)
    {
       return $this->insertGetId(
           [
               'client_id' => $data['client_id'],
               'form_fields' => json_encode($data['fields']),
               'created_by' => $data['created_by'],
               'formname' =>  $data['formname'],
               'utility_id' =>  $data['utility_id'],
               // 'program_id' =>  $data['program_id'],
               'created_at' =>  date('Y-m-d H:i:s'),
               'workspace_id' => $data['workspace_id'],
               'workflow_id' => $data['workflow_id'],
               'commodity_type' => $data['commodity_type'],
               'multienrollment' => $data['multienrollment'],
               

          ]
       );
    }
    public function updateForm($formid,$data)
    {
        
       return $this -> where('id',$formid)
                    ->update( array(
                        'form_fields' =>  json_encode($data['fields']),
                        'formname' =>  $data['formname'],
                        'utility_id' =>  $data['utility_id'],
                        // 'program_id' =>  $data['program_id'],
                        'workspace_id' => $data['workspace_id'],
                        'workflow_id' => $data['workflow_id']
                        )
                        );
    }

    public function deleteForm($formid)
    {
       return $this->where('id', '=', $formid)->delete();
    }

    public function client()
    {
        return $this->belongsTo('App\models\Client', 'client_id');
    }

    public function fields()
    {
        return $this->hasMany('App\models\FormField', 'form_id');
    }

    public function commodities()
    {
        return $this->belongsToMany('App\models\Commodity', 'form_commodities', 'form_id', 'commodity_id');
    }

    public function scripts()
    {
        return $this->hasMany('App\models\FormScripts', 'form_id');
    }

    // public static function boot()
    // {
    //     parent::boot();
    //     static::deleting(function($form)
    //     {
    //         $form->fields()->delete();
    //     });
    // }

}
