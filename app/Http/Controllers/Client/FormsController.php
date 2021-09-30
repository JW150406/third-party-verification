<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\models\Client;
use Auth;
use App\models\Clientsforms;
use App\models\FormField;
use App\models\FormScripts;
use App\models\Commodity;
use App\models\Telesales;
use Illuminate\Support\Facades\View;

class FormsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($clientId, Request $request)
    {     
        /* Start Validation rule */
        $this->validate($request, [
                'formname' => 'required',
                // 'commodity_type' => 'required',
            ],
            [
                'formname.required' => 'This field is required',
                'formname.unique' => 'This form name is taken',
            ]
        );
        /* End Validation rule */

        try {

            DB::beginTransaction();

            $client = Client::find($clientId);

            if (isset($request->preview) && !empty($request->preview)){
                return view('client.form.preview');
            }

            if ($request->get('id')) {
                $form = Clientsforms::find($request->get('id'));
                $form->update($request->only(['formname', 'workspace_id', 'workflow_id', 'description', 'channel','multienrollment']));
                $message = "Form successfully updated.";
            } else {
                $form = $client->forms()->create($request->only(['formname', 'workspace_id', 'workflow_id', 'description', 'channel','multienrollment']));
                $message = "Form successfully created.";
            }

            $commodities = $request->get('commodities');

            $form->commodities()->sync($commodities);

            $formFields = $request->get('field');
            $isClone = $request->input('is_clone',false);
            $updatedFields = [];

            $elmPos = 0;
            foreach ($formFields as $key => $field) {
                $elmPos = $elmPos + 1;
                foreach ($field as $fiKey => $fiValue) {
                    $metaArr = [];
                    if ($meta = array_get($fiValue, 'meta')) {
                        if ($options = array_get($meta, 'options')) {
                            $newOptionArr = [];
                            foreach ($options as $opKey => $opValue) {
                                $optionArr = [];
                                $optionArr['option'] = $opValue;
                                $optionArr['selected'] = false;
                                $newOptionArr[] = $optionArr;
                            }
                            $fiValue['meta']['options'] = $newOptionArr;
                        }
                    }

                    if ($fiValue['id'] && $fiValue['id'] != "" && !$isClone) {

                        $fieldData = FormField::find($fiValue['id']);
                        $fieldData->label = array_get($fiValue, 'label', NULL);
                        $fieldData->type = $fiKey;
                        $fieldData->meta = (isset($fiValue['meta'])) ? ($fiValue['meta']) : null;
                        $fieldData->is_required = array_get($fiValue, 'is_required') ? 1 : 0;
                        $fieldData->is_primary = array_get($fiValue, 'is_primary') ? 1 : 0;
                        $fieldData->is_verify = array_get($fiValue, 'is_verify') ? 1 : 0;
                        $fieldData->is_allow_copy = array_get($fiValue, 'is_allow_copy') ? 1 : 0;
                        $fieldData->is_auto_caps = array_get($fiValue, 'is_auto_caps') ? 1 : 0;
                        $fieldData->is_multienrollment = array_get($fiValue, 'is_multienrollment') ? 1 : 0;
                        $fieldData->position = $elmPos;
                        $fieldData->regex = array_get($fiValue, 'regex');
                        $fieldData->regex_message = array_get($fiValue, 'regex_message');
                        $fieldData->save();
                        $updatedFields[] = array_get($fieldData, 'id');

                    } else {
                        $createdField = $form->fields()->create([
                            'label' => array_get($fiValue, 'label', NULL),
                            'type' => $fiKey,
                            'meta' => array_get($fiValue, 'meta') ? ($fiValue['meta']) : null,
                            'is_required' => array_get($fiValue, 'is_required') ? 1 : 0,
                            'is_primary' => array_get($fiValue, 'is_primary') ? 1 : 0,
                            'is_verify' => array_get($fiValue, 'is_verify') ? 1 : 0,
                            'is_allow_copy' => array_get($fiValue, 'is_allow_copy') ? 1 : 0,
                            'is_auto_caps' => array_get($fiValue, 'is_auto_caps') ? 1 : 0,
                            'is_multienrollment' => array_get($fiValue, 'is_multienrollment') ? 1 : 0,
                            'position' => $elmPos,
                            'regex' => array_get($fiValue, 'regex'),
                            'regex_message' => array_get($fiValue, 'regex_message'),
                            'created_by' => Auth::user()->id
                        ]);
                        $updatedFields[] = array_get($createdField, 'id');                        

                    }
                }
            }

            FormField::whereNotIn('id', $updatedFields)->where('form_id', $form->id)->delete();

            DB::commit();
            \Log::info("Client form created with id: " . $form);
            return redirect()->route('client.edit', array($client->id, '#EnrollmentForm'))->with('success', $message);

        } catch (\Exception $e) {
            \Log::error("Error while creating form: " . $e);
            return redirect()->route('client.contact-page-layout', array($client->id, $form->id))->with('error', $e->getMessage());
        }


    }    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $formFields =FormField::where('form_id',$request->form_id)->orderBy('position', 'asc')->get();
        $forms = Clientsforms::find($request->form_id);
        $commodities = !empty($forms) ? $forms->commodities : [];
        $updatedFields = [];
        foreach ($formFields as $key => $field) {
            if(!empty($field->meta) && isset($field->meta['options'])) {

               $meta['options'] = array_column($field->meta['options'],'option');
            } else {
                $meta = null;
            }
           $updatedFields[] =  [
                    'type' => $field->type,
                    'label' => $field->label,
                    'meta' => $meta
                ];
        }

        $data = View::make('client.forms.preview', ['fields' => $updatedFields,'view'=>true,'commodities'=>$commodities])->render();

        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getAllTags($id) {

        try {
            
          $tags = config('constants.NEW_COMMON_FIELDS_FOR_SCRIPT');
          if ($id > 0) {
            $form = Clientsforms::find($id);

            if(empty($form)) {
              return $this->error('false', 'Form not found', 400);
            }

            $formname = $form->formname;
            $commodity = $form->commodities;
            
            /* for get enable custom fields for program */
            $customFields =  getEnableCustomFields($form->client_id);
            
            foreach ($commodity as $com) {
                array_push($tags, '[Brand -> '. $com->name. ']');
                array_push($tags, '[Brand Contact -> '. $com->name. ']');
                array_push($tags, '[Rate -> '. $com->name. ']');
                array_push($tags, '[Rate In Cent -> '. $com->name. ']');
                array_push($tags, '[Rate In Text -> '. $com->name. ']');

                array_push($tags, '[Term -> '. $com->name. ']');
                array_push($tags, '[MSF -> '. $com->name. ']');
                array_push($tags, '[MSF In Text -> '. $com->name. ']');
                array_push($tags, '[ETF -> '. $com->name. ']');
                array_push($tags, '[ETF In Text -> '. $com->name. ']');
                array_push($tags, '[Utility -> '. $com->name. ']');
                array_push($tags, '[Utility Abbreviation -> '. $com->name. ']');
                array_push($tags, '[Program -> '. $com->name. ']');
                array_push($tags, '[Plan Name -> '. $com->name. ']');
                array_push($tags, '[Program Code -> '. $com->name. ']');

                array_push($tags, '[Unit -> '. $com->name. ']');
                array_push($tags, '[Customer Type -> '. $com->name. ']');
                array_push($tags, '[Account Number Type -> '. $com->name. ']');

                foreach ($customFields as $key => $customField) {
                    array_push($tags, '['.ucwords($customField).' -> '. $com->name. ']');
                }
            }
            $fields = $form->fields;
            
            foreach ($fields as $field) {
                $array = [];
                if ($field->type == 'fullname'){
                    array_push($tags, '['. $field->label. ']');
                    array_push($tags, '['. $field->label. ' -> First Name]');
                    array_push($tags, '['. $field->label. ' -> Middle Name]');
                    array_push($tags, '['. $field->label. ' -> Last Name]');
                } elseif ($field->type == 'address') {
                    array_push($tags, '['. $field->label. ']');
                    array_push($tags, '['. $field->label. ' -> UnitNumber]');
                    array_push($tags, '['. $field->label. ' -> AddressLine1]');
                    array_push($tags, '['. $field->label. ' -> AddressLine2]');
                    array_push($tags, '['. $field->label. ' -> ZipCode]');
                    array_push($tags, '['. $field->label. ' -> City]');
                    array_push($tags, '['. $field->label. ' -> County]');
                    array_push($tags, '['. $field->label. ' -> State]');
                    array_push($tags, '['. $field->label. ' -> Country]');
                    array_push($tags, '['. $field->label. ' -> Latitude]');
                    array_push($tags, '['. $field->label. ' -> Longitude]');
                } elseif ($field->type == 'service_and_billing_address') {
                    array_push($tags, '['. $field->label. ']');
                    array_push($tags, '['. $field->label. ' -> Service Address]');
                    array_push($tags, '['. $field->label. ' -> ServiceUnitNumber]');
                    array_push($tags, '['. $field->label. ' -> ServiceAddressLine1]');
                    array_push($tags, '['. $field->label. ' -> ServiceAddressLine2]');
                    array_push($tags, '['. $field->label. ' -> ServiceZipCode]');
                    array_push($tags, '['. $field->label. ' -> ServiceCity]');
                    array_push($tags, '['. $field->label. ' -> ServiceCounty]');
                    array_push($tags, '['. $field->label. ' -> ServiceState]');
                    array_push($tags, '['. $field->label. ' -> ServiceCountry]');
                    array_push($tags, '['. $field->label. ' -> ServiceLatitude]');
                    array_push($tags, '['. $field->label. ' -> ServiceLongitude]');

                    array_push($tags, '['. $field->label. ' -> Billing Address]');
                    array_push($tags, '['. $field->label. ' -> BillingUnitNumber]');
                    array_push($tags, '['. $field->label. ' -> BillingAddressLine1]');
                    array_push($tags, '['. $field->label. ' -> BillingAddressLine2]');
                    array_push($tags, '['. $field->label. ' -> BillingZipCode]');
                    array_push($tags, '['. $field->label. ' -> BillingCity]');
                    array_push($tags, '['. $field->label. ' -> BillingCounty]');
                    array_push($tags, '['. $field->label. ' -> BillingState]');
                    array_push($tags, '['. $field->label. ' -> BillingCountry]');
                    array_push($tags, '['. $field->label. ' -> BillingLatitude]');
                    array_push($tags, '['. $field->label. ' -> BillingLongitude]');
                } elseif ($field->type == 'label' || $field->type == 'separator' || $field->type == 'heading') {
                } else {
                    array_push($tags, '['. $field->label. ']');
                }
            }
          }
          $newTags = [];
          foreach ($tags as $tag) {
              $newTags[] = strtoupper($tag);
          }
          return $this->success(true, 'success', $newTags);
        } catch (\Exception $e) {
            \Log::error($e);
            return response([
                'status' => false,
                'message' => 'Whoops, Something went wrong.'
            ]);
        }
    }

    /**
     * This method is used to change status
     */
    public function changeStatus(Request $request) {
        if($request->status == 'delete')
        {
            $pendingLeads = Telesales::where('form_id',$request->id)->where('status','pending')->get();
            if($pendingLeads->count() > 0)
            {
                return response()->json([ 'status' => 'error',  'message'=>'This enrollment form cannot be deleted, as there are pending leads of this form.']);   
            }
            else
            {
                try{
                    $forms = Clientsforms::find($request->id);
                    if($forms)
                    {
                        DB::beginTransaction();
                        FormScripts::where('form_id',$request->id)->delete();
                        Clientsforms::where('id',$request->id)->delete();
                        DB::commit();
                        return response()->json([ 'status' => 'success',  'message'=>'Enrollment form successfully deleted.']);
                    }
                    else
                    {
                        return $this->success('success','Something went wrong, please try again later.');
                    }
                } catch (\Exception $e) {
                    Log::error($e);
                    DB::rollback();
                    return response()->json(['status' =>'error','message'=>'Something went wrong, please try again.']);
                }
            }
        }
        else
        {
            Clientsforms::where('id', $request->id)->update(['status'=>$request->status]);
            return response()->json([ 'status' => 'success',  'message'=>'Enrollment form successfully updated.']);
        }
    }

    /**
     * This method is used to show clientform preview
     */
    public function preview(Request $request){
        try {
            $formFields = $request->get('field');

            if (empty($formFields)) {
                return response()->json(['status' => 'error', 'message' => "No form fields available", 'view' => ""]);
            }

            $updatedFields = [];
            $elmPos = 0;
            foreach ($formFields as $key => $field) {
                $elmPos = $elmPos + 1;
                foreach ($field as $fiKey => $fiValue) {
                    $updatedFields[] =  [
                        'type' => $fiKey,
                        'label' => array_get($fiValue, 'label', NULL),
                        'meta' => (isset($fiValue['meta'])) ? ($fiValue['meta']) : null
                    ];
                }
            }
            $commodityIds = (array) $request->commodities;
            $commodities =Commodity::whereIn('id',$commodityIds)->get();

            $data = View::make('client.forms.preview', ['fields' => $updatedFields, 'commodities' => $commodities])->render();
            return response()->json(['status' => 'success', 'message' => 'success', 'view' => $data]);
        } catch(\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'view' => ""]);
        }
    }

    public function checkFormNameExist(Request $request) {
        return response([
            'exists' => boolval(Clientsforms::where('client_id', $request->client_id)->where('formname', '=', $request->formname)->count())
        ]);
    }

    /**
     * This method is used to get clientform
     */
    public function getClientForms(Request $request) {
        try {
            if (isset($request->client_id) && !empty($request->client_id)) {
                $client = Client::find($request->client_id);

                if ($client) {
                    $forms = $client->forms;

                    return response([
                        'status' => true,
                        'data' => $forms
                    ]);
                }
            }
        } catch (\Exception $e) {
            return response([
                'status' => false,
                'message' => 'Whoops, Something went wrong.'
            ]);
        }
    }

}
