<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Client;
use App\models\Clientsforms;
use Illuminate\Support\Facades\Auth;
use App\User; 
use Illuminate\Routing\UrlGenerator;
use App\models\FormScripts;

class FormScriptsController extends Controller
{
    public $client_id = "";
    public $form_id = "";
    public $client_detail = "";
    public $form_detail = "";
    public $client_obj = array();
    public $FormScripts_obj = array();

    public function __construct(Request $request){
        $this->client_id = $request->client_id;
        $this->form_id = $request->form_id;
        if( empty($this->client_id) || empty($this->form_id ) ){    
            
            abort(403);
        }else{
            Client::findOrFail($this->client_id);
            ClientsForms::findOrFail($this->form_id);
        }
        $this->client_obj = (new Client);          
        $this->client_detail = $this->client_obj->getClientinfo( $this->client_id );
        $this->form_detail = (new ClientsForms)->getClientFormFields($this->form_id);
        $this->FormScripts_obj =  (new FormScripts);
        
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        $languages = $this->FormScripts_obj->languages; 
        $client = $this->client_detail;
        $client_id = $this->client_id;
        $form_id = $this->form_id;
        $form_detail = $this->form_detail[0];
        return view('client.forms.scripts.index',compact('client_id','client','form_id','form_detail','languages'));
    }

    /**
     * This method is used to show form scriptlist
     */
    public function scriptsList(Request $request){
        $language = $request->language;

        $scripts_list =  $this->FormScripts_obj->scripts_list($this->form_id,$language);
        $client = $this->client_detail;
        $client_id = $this->client_id;
        $form_id = $this->form_id;
        $form_detail = $this->form_detail[0];
        $languages = $this->FormScripts_obj->languages; 
        $script_for = $this->FormScripts_obj->script_for; 
      
        return view('client.forms.scripts.scriptslist',compact('client_id','scripts_list','client','form_id','form_detail','languages','language','script_for'))
        ->with('i', ($request->input('page', 1) - 1) * 20);
    }

    /**
     * This method is used to create new script
     */
    public function newScript(Request $request){
        $client = $this->client_detail;
        $client_id = $this->client_id;
        $form_id = $this->form_id;
        $form_detail = $this->form_detail[0];
        $languages = $this->FormScripts_obj->languages;  
        $language = $request->language;
        $script_for = $this->FormScripts_obj->script_for; 
        return view('client.forms.scripts.createscript',compact('script_for','client_id','client','form_id','form_detail','languages','language'));
    }

    /**
     * This function is used to store script
     */
    public function saveScript(Request $request){
       

           $validator = \Validator::make($request->all(), [
            'language' => 'required',
             'title' => 'required',              
        ]);
        
        if ($validator->fails())
        {
            return response()->json([ 'status' => 'error',  'errors'=>$validator->errors()->all()]);
        }
        try{

           $data = $request->only('language','title','scriptfor','client_id','form_id');
           $data['created_by'] =  Auth::user()->id;
           $added =  $this->FormScripts_obj->createScript($data); 
           if( $added > 0 ) {            
            // return redirect()->route('client.add-script-questions', ['client_id' =>$this->client_id, 'form_id' => $this->form_id, 'script_id' =>$added])
            // ->with('success','Script created successfully. Add new question');

            return response()->json([ 'status' => 'success',  'message'=>'Script created successfully. Add new question.','url' => route('client.add-script-questions', ['client_id' =>$this->client_id, 'form_id' => $this->form_id, 'script_id' =>$added])]);

           }else{
            return response()->json([ 'status' => 'error',  'errors'=> ["Something went wrong!. Please try again."]]);
           } 

          
        } catch(Exception $e) {
         // echo 'Message: ' .$e->getMessage();
          return response()->json([ 'status' => 'error',  'errors'=> ["Something went wrong!. Please try again."]]);
        }     
     }

     /**
      * This method is used to show edit formscript
      */
     public function editScript(Request $request){
       
        $client = $this->client_detail;
        $client_id = $this->client_id;
        $form_id = $this->form_id;
        $script_id = $request->script_id;
        $script_detail =  $this->FormScripts_obj->getScript($client_id, $form_id, $script_id );
        $language = $script_detail->language;
        $form_detail = $this->form_detail[0];
        $languages = $this->FormScripts_obj->languages;  
        $script_for = $this->FormScripts_obj->script_for; 
        return view('client.forms.scripts.editscript',compact('script_for','client_id','client','form_id','form_detail','languages','script_detail','language'));
    }

    /**
     * This method is used to update form script
     */
    public function updateScript(Request $request){
        $this->validate($request, [
            'language' => 'required',
             'title' => 'required',            
           ]);
           $data = $request->only('language','title','scriptfor');
           $this->FormScripts_obj->updateScript($request->script_id,$data); 
            return redirect()->back()
            ->with('success','Script created successfully.');             
     }

     /**
      * This method is used to remove form script
      */
     public function deleteScript(Request $request){
        $script_id = $request->script_id;
        $this->FormScripts_obj->getScript($this->client_id, $this->form_id, $script_id );
        $this->FormScripts_obj->deleteScript($script_id);
         return redirect()->back()
            ->with('success','Script deleted successfully.'); 
     }
     
     /**
      * This function is used to show formscript list
      */
    public function list(Request $request) {
        $scripts = FormScripts::where('client_id', $request->get('clientId'));
        return DataTables::of($scripts)
                ->addColumn('action', function($utility){
                    return '<div class="btn-group">
                        <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="View" role="button" class="btn">
                            <img src="http://localhost:8000/images/view.png">
                        </a>
                        <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" data-container="body" title="" data-original-title="Edit" role="button" class="btn">
                            <img src="http://localhost:8000/images/Edit.png">
                        </a>
                    </div>';
                    });
    }
    
    
}
