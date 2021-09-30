<?php

namespace App\Http\Controllers\Utility;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Brandcontacts;
use App\models\Utilities;
use DataTables;
class BrandContactsController extends Controller
{
    //
    Public $brandcontacts = array();
    function __construct(){
        $this->brandcontacts = (new Brandcontacts);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request){
        $brandnames = (New Utilities)->GetDistinctNames(); 
        $utilities = array();
        $contacts_list =  $this->brandcontacts->GetContacts(); 
         
        return view('client.utilities.brandcontacts.index',compact('brandnames','contacts_list'))
            ->with('i', ($request->input('page', 1) - 1) * 20);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function savenewcontact(Request $request){
       
        if( isset($request->brandname) &&  isset($request->contact)){
              $savedata =  array(
                    'name' => $request->brandname,
                    'contact' => $request->contact,
                    'created_at' => 'now()',
                    'updated_at' => 'now()',
              );

            $inserted =   $this->brandcontacts->savecontact($savedata);
            if( $inserted > 0 ){
                return redirect()->back()
                ->with('success','Brand Contact successfully Saved.');
            }else{
                return redirect()->back()->withErrors(['msg', 'Invalid request']);
            }

                 
        }
         

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function editcontact($id,Request $request){
       
       $detail = $this->brandcontacts->getBrandContact($id);
       
   
         
        return view('client.utilities.brandcontacts.edit',compact('detail'));
    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function updatecontact($id,Request $request){

        if( isset($request->name) &&  isset($request->contact)){
            $savedata =  array(
                  'name' => $request->name,
                  'contact' => $request->contact, 
                  'updated_at' => 'now()',
            );

          $this->brandcontacts->updatecontact($savedata,$id);
         
              return redirect()->back()
              ->with('success','Contact successfully updated.'); 
          }else{
              return redirect()->back()->withErrors(['msg', 'Invalid request']);
          }

               
      }

       /**
     * Remove the specified resource from storage.
     * @return \Illuminate\Http\Response
     */
      public function deletecontact(Request $request){

        if( isset($request->id) ){
            

          $this->brandcontacts->deletecontact($request->id);
         
              return redirect()->back()
              ->with('success','Contact successfully deleted.'); 
          }else{
              return redirect()->back()->withErrors(['msg', 'Invalid request']);
          }

               
      }


      /**
       * This method is used to show brand list
       */
    public function list(Request $request) 
    {
        if ($request->ajax()) {

            
            $contacts = Brandcontacts::where('client_id',$request->client_id);

            return DataTables::of($contacts)
                
                ->addColumn('action', function($contact){
                    $editBtn = $deleteBtn = '';
                    if(auth()->user()->hasPermissionTo('edit-brand-contact')) {
                        $editBtn = '<a 
                        href="javascript:void(0)"  
                        data-toggle="tooltip" 
                        data-placement="top" data-container="body" 
                        title="Edit Brand Contact" 
                        data-original-title="Edit Brand Contact" 
                        role="button" 
                        data-type="edit"
                        data-id="' . $contact->id . '" 
                        class="btn brand-contact-create-modal">'
                            . getimage("images/edit.png") . '</a>';
                    }
                    if(auth()->user()->hasPermissionTo('delete-brand-contact')) {
                        $deleteBtn = '<a 
                        href="javascript:void(0)"  
                        data-toggle="tooltip" 
                        data-placement="top" data-container="body" 
                        title="Delete Brand Contact" 
                        data-original-title="Delete Brand Contact" 
                        role="button" 
                        data-id="' . $contact->id . '" 
                        data-url="' . route("brand-contact.destroy", $contact->id) . '" 
                        class="btn delete-brand-contact">'
                            . getimage("images/cancel.png") . '</a>';
                    }
                    if(empty($editBtn) && empty($deleteBtn)) {
                        return '';
                    } else {
                        return '<div class="btn-group">'.$editBtn.$deleteBtn.'<div>';
                    }
                })
                ->make(true);
        }
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /* Start Validation rule */
        $request->validate([
            'name' => 'required|unique:brand_contacts,name,'.$request->id.',id,client_id,'.$request->client_id,
            'contact' => 'required|unique:brand_contacts,contact,'.$request->id.',id,client_id,'.$request->client_id
        ],['name.unique' => "The brand has already been taken."]);
        /* End Validation rule */
        try {
            // dd($request->all());
            Brandcontacts::updateOrCreate(['id'=>$request->id],[
                'client_id' => $request->client_id,
                'name' => $request->name,
                'contact' => $request->contact,
            ]);
            return response()->json([ 'status' => 'success',  'message'=>'Brand Contact successfully Saved']);
        } catch(Exception $e) {
            return response()->json([ 'status' => 'error',  'message'=> 'Something went wrong, please try again.']);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Brandcontacts::where('id',$id)->delete();
        return response()->json([ 'status' => 'success',  'message'=>'Brand Contact successfully deleted']);
    }

    /**
     * This method is used to get brand
     */
    public function getBrands($clientId)
    {
        $data = Brandcontacts::where('client_id',$clientId)->get();
        return $this->success('success','success',$data);
    }
    

    
}
