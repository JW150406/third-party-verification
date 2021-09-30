<?php

namespace App\Http\Controllers\Utility;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Client;
use App\models\Utilities;
use App\models\Clientsforms;
use App\models\ComplianceTemplates;
use Illuminate\Support\Facades\Auth;

class ComplianceController extends Controller
{
    public $client_id = "";
    public $utility_id = "";
    public $client_detail = "";
    public $utilities_detail = "";
    public $client_obj = "";
    public $utilities_obj = "";
    public $complianceTemplates = array();
    public $clientsforms = array();
    public function __construct(Request $request){

      if( !isset($request->client_id) ){       
           abort(403);
      }else{
          $this->client_id = $request->client_id;
          Client::findOrFail($this->client_id);
      }
      if( !isset($request->utility_id) ){
               abort(403);
          }else{
              $this->utility_id = $request->utility_id;
              Utilities::findOrFail($this->utility_id);
          }

      
     $this->complianceTemplates = (new ComplianceTemplates);
     $this->client_obj = (new Client);
     $this->utilities_obj = (new Utilities);
     $this->clientsforms = (new ClientsForms);
     $this->client_detail = $this->client_obj->getClientinfo( $this->client_id );
     $this->utilities_detail = $this->utilities_obj->getUtility( $this->utility_id );
     
    }

    /**
     * This method is used to show compliance template list
     */
    function templates (Request $request){
      $client_id = $this->client_id;
      $client = $this->client_detail;
      $utility = $this->utilities_detail;
      $ids  = array($utility->id);
      
      $templates =  $this->complianceTemplates->utilitiestemplateslistadmin($this->client_id,$ids);
      return view('client.compliance.templatesList',compact('client_id','templates','client','utility'))
      ->with('i', ($request->input('page', 1) - 1) * 20);
    }

    /**
     * This method is used to add compliance template
     */
    function addtemplate(Request $request){
      $client_id = $this->client_id;
      $client = $this->client_detail;
      $utility = $this->utilities_detail;
      $forms = $this->clientsforms->getAllFormUsingClientIDandUtilityID($client_id,$utility->id);
      return view('client.compliance.addtemplate',compact('client_id','forms','client','utility'));
    }

    function mapoptions(Request $request){
        $fields_headers = $request->input('fields');
        $header_options = explode(PHP_EOL, $fields_headers);
        $elements_in_form = $this->get_form_options($request->input('fid'));
        return view('client.compliance.ajaxfieldsmaping',compact('header_options','elements_in_form'));

    }

    function get_form_options($form_id){
      $form_detail = $this->clientsforms->getClientFormFields($form_id);
      $form_detail = $form_detail[0];

      $fields = json_decode($form_detail->form_fields);
      $elements_in_form = "";
      $elements_in_form.= "<option value='TPV Agent'>TPV Agent</option>";
      $elements_in_form.= "<option value='Sales Agent'>Sales Agent</option>";
      $elements_in_form.= "<option value='Sales Center'>Sales Center</option>";
      $elements_in_form.= "<option value='Location'>Location</option>";
      $elements_in_form.= "<option value='Status'>Status</option>";
      $elements_in_form.= "<option value='Location'>Location</option>";
      $elements_in_form.= "<option value='Create Time'>Create Time</option>";
      $elements_in_form.= "<option value='Update Time'>Update Time</option>";
      $elements_in_form.= "<option value='Disposition'>Disposition</option>";
      foreach($fields as $field){
          if( !empty($field->label_text))
          $elements_in_form.= "<option value='".$field->label_text."'>{$field->label_text}</option>";
      }
      return $elements_in_form;
    }

    /**
     * This method is used to store compliance template
     */
    function savetemplate(Request $request){

         $input = $request->only('client_id','name','form_id','header_column','utility_id');
         $input['created_by'] = Auth::user()->id;
         $input['fields'] = serialize($input['header_column']);
          try{
           $this->complianceTemplates->addtemplate($input);
           return response()->json([ 'status' => 'success',  'message'=>'Template successfully created.','url' => route('client.utility.Compliances',['client_id' => $this->client_id, 'utility_id' => $this->utility_id ])]);
        //    return redirect()->route('utility.compliance-add-templates',['client_id' => $this->client_id, 'utility_id' => $this->utility_id ])
        //        ->with('success','Template successfully created.');
          }
          catch(Exception $e){
            return response()->json([ 'status' => 'error',  'errors'=> ["Something went wrong!. Please try again."]]);
            // return redirect()->back()
            // ->withErrors('error','Something went wrong!. Please try again.');
          }
    }

    /**
     * Show the form for editing the specified resource.
     * @return \Illuminate\Http\Response
     */
    function edittemplate(Request $request){
      $client_id = $this->client_id;
      $client = $this->client_detail;
      $utility = $this->utilities_detail;
      $forms =$this->clientsforms->getAllFormUsingClientIDandUtilityID($client_id,$utility->id);
      $template =  $this->complianceTemplates->gettemplate($request->id);
      $fields_option =  $this->get_form_options($template->form_id);
      
      return view('client.compliance.edittemplate',compact('client_id','forms','client','template','fields_option','utility'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function updatetemplate(Request $request){
         $input = $request->only('client_id','name','form_id','header_column');
         $input['fields'] = (isset($input['header_column'])) ? serialize($input['header_column']) : serialize( array(
           'header' => array(),
           'values' => array(),
         ));
          try{
           $this->complianceTemplates->updatetemplate($request->id,$input);
           return redirect()->back()
               ->with('success','Template successfully updated.');
          }
          catch(Exception $e){
            return redirect()->back()
            ->withErrors('error','Something went wrong!. Please try again.');
          }
    }

     /**
     * Remove the specified resource from storage.
     * @return \Illuminate\Http\Response
     */
    public function deletetemplate(Request $request){
        $id = $request->id;
        $this->complianceTemplates->deleteTemplate($id);
        return redirect()->back()
            ->with('success','Template deleted successfully.');
     }

     function ajaxComplianceTemplates(Request $request){
     
    
      if(isset($request->client_id) && isset($request->utility_id) ){
         $client_id = $request->client_id;
       $utility_ids = explode(',',$request->utility_id); 
       
        $templates =  $this->complianceTemplates->allutilitiestemplateslist($client_id,$utility_ids);
      
        
        $res_options = array();
        if(count($templates) > 0){
            foreach($templates as $template){       
                $res_options[] = array(
                    'id' => $template->id,
                    'name' => $template->name
                );      
              // $res_options.="<option value=\"$template->id\">".$template->name ."</option>";
                // $res_options.= "<tr>";
                // $res_options.= "<td><input type='checkbox' value='".$template->id."'></td>";
                // $res_options.= "<td>".$template->name."</td>";
                // $res_options.= "<td>view $template->id</td>";            
                // $res_options.= "</tr>";
            }
        }
        
        $response = array(
            'status' => 'success',
            'options' =>  $res_options,
        );
    }else{
        $response = array(
            'status' => 'error',
            'message' =>  "Invalid Request",
        );
    }
   
    return \Response::json($response);
       
     }





}
?>
