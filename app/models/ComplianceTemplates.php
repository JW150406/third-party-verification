<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ComplianceTemplates extends Model
{
    protected $table = 'compliance_templates';

    function templateslistadmin($client_id){
        return $this->select('compliance_templates.*','clientsforms.formname')
         ->join('clientsforms', 'clientsforms.id', '=', 'compliance_templates.form_id')
        ->where([
            ['compliance_templates.client_id', '=', $client_id],
        ])->paginate(20);
    }
    function utilitiestemplateslistadmin($client_id,$utility_id){
        return $this->select('compliance_templates.*','clientsforms.formname')
         ->join('clientsforms', 'clientsforms.id', '=', 'compliance_templates.form_id')
        ->where([
            ['compliance_templates.client_id', '=', $client_id]
        ])->whereIn('compliance_templates.utility_id', $utility_id)
        ->paginate(20);
    }
    function allutilitiestemplateslist($client_id,$utility_ids){
        return $this->select('compliance_templates.*','clientsforms.formname')
         ->join('clientsforms', 'clientsforms.id', '=', 'compliance_templates.form_id')
        ->where([
            ['compliance_templates.client_id', '=', $client_id],
        ])->whereIn('compliance_templates.utility_id', $utility_ids)
        ->get();
    }
    function addtemplate($data){
      return $this->insertGetId(
          [
              'client_id' => $data['client_id'],
              'form_id' => $data['form_id'],
              'utility_id' => $data['utility_id'],
              'created_by' => $data['created_by'],
              'name' =>  $data['name'],
              'fields' =>  $data['fields'],
              'created_at' =>  date('Y-m-d H:i:s'),
         ]
      );
    }
    function updatetemplate($id,$data){
      return $this->where('id',$id)
                   ->update( array(
                     'client_id' => $data['client_id'],
                     'form_id' => $data['form_id'],
                     'name' =>  $data['name'],
                     'fields' =>  $data['fields'],
                     'updated_at' =>  date('Y-m-d H:i:s'),
                       )
                   );

    }
    function templateslist($client_id){
        return $this->select('id','name')
        ->where([
            ['client_id', '=', $client_id],
        ])->get();
    }

     public function deleteTemplate($id){
       return $this->where('id', '=', $id)->delete();
     }
    public function gettemplate($id)
    {
       return $this->where([
             ['id', '=', $id],
         ])->firstOrFail();
    }
}
