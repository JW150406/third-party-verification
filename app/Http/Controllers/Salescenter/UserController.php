<?php

namespace App\Http\Controllers\Salescenter;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

use App\User;
use App\models\Role;
use App\models\Client;
use App\models\Salescenter;
use App\models\TextEmailStatistics;
use App\models\Salescenterslocations;
use App\Mail\SendVerificationEmail;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use Hash;
use Log;

class UserController extends Controller
{
    /**
     * use of bulk upload for sales center user
     * @param  $clientId
     * @param  $salescenterId
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function bulkUpload($clientId, $salescenterId)
    {
        $client = Client::active()->findOrFail($clientId);
        $salescenter = Salescenter::active()->findOrFail($salescenterId);
        return view('client.salescenter_new.user.bulkupload',compact('client', 'salescenter'));
    }
    
    /**
     * use of download sample file for sales center user
     * @param Request $request
     * @return csv file
     */
    public function downloadSample(Request $request) {
        $data =[[
            "Sales Center" => "Intersoft",
            "Location" => "Location1",
            "First Name" => "John",
            "Last Name" => "Doe",
            "Email" => "jhon@gmail.com",
            "Role" => "Sales Center Admin",
        	],[
            "Sales Center" => "Intersoft",
            "Location" => "Location2",
            "First Name" => "Lisa",
            "Last Name" => "Doe",
            "Email" => "lisa@gmail.com",
            "Role" => "Sales Center QA",
        ]];

        return Excel::create('salescenter_user_sample', function($excel) use ($data) {
            $excel->sheet('sheet1', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download("csv");
    }
    
    /**
     * use of import for sales center user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request) {
        $clientId=$request->client_id;
        $salescenterId = $request->salescenter_id;

        /* Start Validation rule */
        $validator = \Validator::make(
            [
                'upload_file' => $request->hasFile('upload_file')? strtolower($request->file('upload_file')->getClientOriginalExtension()) : null,
            ],
            [
                'upload_file'      => 'required|in:csv,xlsx,xls',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['status' => 'error',  'errors' => $validator->errors()->messages()], 422);
        }
        /* End Validation rule */

        try {

            $path = $request->file('upload_file')->getRealPath();
            $data = Excel::load($path, function($reader) {
                $columns= [
                        "sales_center",
                        "location",
                        "first_name",
                        "last_name",
                        "email",
                        "role"
                    ];
                $reader->select($columns);
            })
            ->ignoreEmpty()
            ->get()
            ->toArray();

            $errors=$validData=array();
            if (empty($data)) {
                $errors[1][]='The file is empty or invalid data.';
                $view = view('client.errors.file-errors', compact('errors'))->render();
                return response()->json(['status' => 'dataErrors',  'errors' => $view], 422);
            }
            foreach ($data as $key => $userData) {
            	if (!empty($userData['location'])) {
            		$userData['location'] = array_map('trim', explode(',', $userData['location']));
            	}
                $messages = [
                	'sales_center.required' => 'The sales center field is required',
                	'first_name.required' => 'The first name field is required',
                	'last_name.required' => 'The last name field is required',
                	'role.required' => 'The role field is required',
                ];
                /* Start Data Validation rule */
                $dataValidator = \Validator::make($userData,
                    [
                    	'sales_center'  => ['required','max:255',
                            Rule::exists('salescenters','name')->where(function ($query) use ($clientId) {
                                $query->where('client_id',$clientId);
                            })],
                        'location'      => ['required_if:role,Sales Center Location Admin,Sales Center QA','max:255',
                            Rule::exists('salescenterslocations','name')->where(function ($query) use ($clientId,$salescenterId) {
                                $query->where('status','active')->where('salescenter_id',$salescenterId)->where('client_id',$clientId);
                            })],
                        'first_name'    => 'required|max:255',
                        'last_name'     => 'required|max:255',
                        'email'         => 'required|email|max:255|unique:users,email',
                        'role'          => ['required',
                            Rule::exists('roles','display_name')->where(function ($query) {
                                $query->where('accesslevel','salescenter');
                            })],
                    ],
                    $messages
                );
                    
                if ($dataValidator->fails()) {
                    foreach ($dataValidator->messages()->all() as  $value) {
                        $errors[$key+1][]=$value;
                    }
                } else {
                    $validData[] = $userData;
                }
            }
            /* End Data Validation rule */

            if (!empty($errors)) {
                $view = view('client.errors.file-errors', compact('errors'))->render();
                return response()->json(['status' => 'dataErrors',  'errors' => $view], 422);
            } else {

                foreach ($validData as $key => $userData) {
                    $verificationCode  = str_random(20);
                	$nextAutoID = (new User)->nextAutoID();
                    $data['client_id'] = $clientId;
                    $data['salescenter_id'] = $salescenterId;
                    $data['parent_id'] = Auth::user()->id;
                    $data['first_name'] = $userData['first_name'];
                    $data['last_name'] = $userData['last_name'];
                    $data['email'] = $userData['email'];
                    $data['access_level'] = 'salescenter';
                    $data['status'] = 'active';
                    $data['verification_code'] = $verificationCode;
                    $data['userid'] = strtolower($userData['first_name'][0]).$nextAutoID;
                    $data['password'] = Hash::make(str_random(8));
                    $user=User::create($data);

                    $role = Role::where('display_name',$userData['role'])->first();
                    if (!empty($role)) {
                    	$user->attachRole($role);
                    }
                    if (isset($userData['location']) && !empty($userData['location'])) {
                		$this->storeUserLocations($user, $userData['location'], $clientId, $salescenterId );
                    }                    
                	$this->sendVerificationEmail($user);
                }
                
                $url = \URL::route('client.salescenter.show',[$clientId,$salescenterId]). '#SalesCenterUser';
                // session()->put('success', 'Sales center user successfully imported.');
                return response()->json(['status' => 'success',  'message' =>'Sales center user successfully imported.', 'url' =>$url], 200);
            }
        } catch(\Exception $e) {
            Log::error('Error while import sales center user:- '.$e);
            return response()->json(['status' => 'error',  'message' => $e->getMessage()], 500);
        }
    }

    /**
     * store user locations
     * @param $user
     * @param $locations
     * @param $clientId
     * @param $salescenterId
     */
    public function storeUserLocations($user, $locations, $clientId, $salescenterId ) {
    	try {
    		$locationIds = Salescenterslocations::whereIn('name',$locations)
    				->where('status','active')
    				->where('client_id',$clientId)
    				->where('salescenter_id',$salescenterId)
    				->pluck('id');
	        if ($user->hasRole("sales_center_qa")) {
	            $user->locations()->sync($locationIds);
	        } else if($user->hasRole("sales_center_location_admin")) {
	            $user->location_id = isset($locationIds[0]) ? $locationIds[0] : 0 ;
	            $user->save();
	        }
	    } catch(\Exception $e) {
            Log::error('Error while store sales center user location:- '.$e);
        }
    }
    
    /**
     * use for export sales center user
     * @param Request $request
     * @return unknown|\Illuminate\Http\RedirectResponse
     */
    public function export(Request $request) {
        try {
            $client = Client::findOrFail($request->client_id);

            $clientName = str_replace(" ", "-", array_get($client, 'name'));

            if ($clientName != "") {
                $fileName = $clientName . "-" . "users";
            } else {
                $fileName = "users";
            }

            $fileName .= "-" . date('d_M_Y_H_i_A');

            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=". $fileName .".csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );
            $users = User::where('client_id',$request->client_id)->where('salescenter_id',$request->salescenter_id)->where('access_level','salescenter')->with('salescenter','location','locations','roles')->get();
            
            $columns = array('Sales Center', 'Location', 'First Name', 'Last Name', 'Email',  'Role');
                             
            $callback = function() use ($users, $columns) {
            	$file = fopen('php://output', 'w');
            	fputcsv($file, $columns);
            	foreach($users as $user) {
            		$salescenter = isset($user->salescenter) ? $user->salescenter->name : '';
            		$location = $user->getLocationName();
            		$role = (isset($user->roles) && !empty($user->roles->first())) ? $user->roles->first()->display_name : '';
            		fputcsv($file, array($salescenter, $location, $user['first_name'], $user['last_name'], $user['email'], $role));
            	}
            	fclose($file);
            };
                    
            return \Response::stream($callback, 200, $headers);
        } catch(\Exception $e) {
        	Log::error('Error while export sales center user:- '.$e);
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

    /**
     * use for send verification email
     * @param $user
     */
    public function sendVerificationEmail($user)
    {
        try {            
            $to      = $user['email'];

            $greeting = 'Hello '.$user['first_name'] .',';
	        $message = 'You have been added to TPV360. <br/>
	            Your username is: '.$to.'. Please <a href="'.url('/'.$user['salescenter_id'].'/verify', ['code'=>$user['verification_code']]) .'">click here</a> to generate your password.';

            \Mail::to($to)->send(new SendVerificationEmail($greeting, $message));

            $textEmailStatistics = new TextEmailStatistics();
            $textEmailStatistics->type = 1;
            $textEmailStatistics->save();
        }catch(\Exception $e) {
            Log::error($e);
        }
    }
}
