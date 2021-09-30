<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Client;
use App\models\Clientsforms;
use App\models\ComplianceTemplates;
use Illuminate\Support\Facades\Auth;

class ComplianceController extends Controller
{
    public $client_id = "";
    public $client_detail = "";
    public $client_obj = "";
    public $complianceTemplates = array();
    public $clientsforms = array();
    public function __construct(Request $request){

      if( !isset($request->client_id) ){
          //abort(403);
      }else{
         $this->client_id = $request->client_id;
          Client::findOrFail($this->client_id);
      }
     $this->complianceTemplates = (new ComplianceTemplates);
     $this->client_obj = (new Client);
     $this->clientsforms = (new ClientsForms);
      $this->client_detail = $this->client_obj->getClientinfo( $this->client_id );
    }

    /**
     * This method is used to get compliance templateslist
     */
    function templates (Request $request){
      $client_id = $this->client_id;
      $client = $this->client_detail;
      $templates =  $this->complianceTemplates->templateslistadmin($this->client_id);
      return view('client.compliance.templatesList',compact('client_id','templates','client'))
      ->with('i', ($request->input('page', 1) - 1) * 20);
    }

    /**
     * This function is used to add compliance template
     */
    function addtemplate(Request $request){
      $client_id = $this->client_id;
      $client = $this->client_detail;
      $forms = $this->clientsforms->getAllFormUsingClientID($client_id);
      return view('client.compliance.addtemplate',compact('client_id','forms','client'));
    }

    /**
     * This method is used to ajax fieldmapping
     */
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

         $input = $request->only('client_id','name','form_id','header_column');
         $input['created_by'] = Auth::user()->id;
         $input['fields'] = serialize($input['header_column']);
          try{
           $this->complianceTemplates->addtemplate($input);
           return redirect()->route('client.compliance-templates',$this->client_id)
               ->with('success','Template successfully created.');
          }
          catch(Exception $e){
            return redirect()->back()
            ->withErrors('error','Something went wrong!. Please try again.');
          }
    }

    /**
     * This method used to show edit compliance template
     */
    function edittemplate(Request $request){
      $client_id = $this->client_id;
      $client = $this->client_detail;
      $forms = $this->clientsforms->getAllFormUsingClientID($client_id);
      $template =  $this->complianceTemplates->gettemplate($request->id);
      $fields_option =  $this->get_form_options($template->form_id);
      return view('client.compliance.edittemplate',compact('client_id','forms','client','template','fields_option'));
    }

    /**
     * This method is used to update compliance template
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
     * This function is used to remove compliance template 
     */
    public function deletetemplate(Request $request){
        $id = $request->id;
        $this->complianceTemplates->deleteTemplate($id);
        return redirect()->back()
            ->with('success','Template deleted successfully.');
     }





}
?>
