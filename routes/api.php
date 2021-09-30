<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', 'API\UserController@login');
Route::post('forgotpassword', 'API\PasswordController@forgotPassword');

/*Route::post('register', 'API\UserController@register');*/


Route::group(['middleware' => ['auth:api','activity:api','isOnD2Dapp:api']], function(){

	Route::post('details', 'API\UserController@details');
	Route::get('get-timezone', 'API\UserController@getTimezone');
	Route::post('update-timezone', 'API\UserController@updateTimezone');
	Route::post('update-profile-photo', 'API\UserController@updateProfilePhoto');
	
	Route::post('logout', 'API\UserController@logout');
	Route::post('dashboard', 'API\UserController@dashboard');
	Route::any('myleads', 'API\UserController@leadlist');
	Route::any('zipautocomplete', 'API\ZipcodeController@getzipcodeslist' );

	// Route for get utility by zipcode
	Route::any('validatezipcode', 'API\ZipcodeController@validatezip' );
	Route::post('getprograms', 'API\ProgramsController@getProgramsFormUtility')->name('api.utility.programs');
	Route::get('clients/{id}/forms', 'API\FormsController@index');
	// Route::post('getform', 'API\ClientsController@contactform');
	Route::post('getform', 'API\FormsController@details');
	Route::post('get-form-settings', 'API\FormsController@formSettings');

	// Route for get states if has utility
	Route::post('get-utility-states', 'API\ZipcodeController@getStates');

	// Route for get regex for validation of account number fields
	Route::post('get-account-number/regex', 'API\FormsController@getRegex');

	Route::post('saveleaddata', 'API\LeadsController@store');
	Route::post('sendcontract', 'API\ClientsController@sendcontract');
	Route::post('leadmedia', 'API\ClientsController@leadmedia');
	Route::post('generateotp', 'API\ClientsController@generateotp');
	Route::post('verifyotp', 'API\ClientsController@verifyotp');
	Route::post('generateotp/email', 'API\ClientsController@generateotpEmail');
	Route::post('verifyotp/email', 'API\ClientsController@verifyotpEmail');

	Route::post('selfverify', 'API\ClientsController@selfverify');

	Route::get('leads/{id}', 'API\LeadsController@show');

	Route::get('cancel-lead/{id}', 'API\LeadsController@cancelLead');
	Route::post('cancel-lead/{id}', 'API\LeadsController@cancelLeadPost');
	

	// Route for check validation of lead data
	Route::post('check-lead-validation', 'API\LeadsController@checkLeadValidation');

	// route for send signature link
    Route::post('send-signature-link', 'API\LeadsController@sendSignatureLink');

    // route for check signature is uploaded or not
    Route::post('verify/signature', 'API\LeadsController@verifySignature');

	Route::post('agent-activity', 'API\SalesAgentActivityController@store');
	Route::get('agent-current-activity', 'API\SalesAgentActivityController@getCurrentActivity');

	// Route for tpv schedule outbound call API
	Route::post('schedule-tpv-call','API\ScheduleCallController@scheduleCall');
});

Route::group(['middleware' => ['auth:api','activity:api','roleCheck:api']], function(){
    // Route for Get All Clients
    Route::get('clients', 'API\ClientsController@index');

    // Route for Get Sales Centers By Client Id
    Route::get('sales-centers', 'API\SalesCenterController@index');

    // Route for Get Critical Alert Report
    Route::post('reports/critical-alerts', 'API\CriticalAlertsReportController@index');

    // Route for View Critical Lead
    Route::post('reports/critical-alerts/{id}/details', 'API\CriticalAlertsReportController@show');    

    // Route for Get Timeline logs for lead
    Route::post('reports/critical-alerts/{id}/timeline', 'API\CriticalAlertsReportController@histroy');
});
Route::post('agent-locations', 'API\SalesAgentActivityController@saveAgentLocation')->middleware(['auth:api','isOnD2Dapp:api']);

//Chatbot APIs
Route::group(['middleware' => 'apiHeader:api'], function(){
	Route::post('data/clients','API\ClientsController@getAllClients');
	Route::post('data/salescenters','API\ClientsController@getAllSalesCenters');
	Route::post('data/locations','API\ClientsController@getSalesCentersLocations');
	Route::post('data/leadscount','API\ClientsController@getLeadStatus');
});

Route::post('api-version', 'API\AppVersionController@index');
