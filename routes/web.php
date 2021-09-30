<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get("/call-api", "Admin\DashboardController@callApi");

Route::get('/mobile-dashboard', 'Admin\DashboardController@mobileDashboard')->name('mobile-dashboard');

Route::any('/', function () {

	$user = \Auth::user();
	if($user->access_level == 'salesagent') {
		return redirect()->route('my-account');
	} else if($user->access_level == 'tpvagent') {
		return redirect()->route('tpvagents.sales');
	} else {
	    return redirect()->intended('/admin/dashboard');
	}
})->middleware(['auth']);




Route::get('selfverify/{verificationid?}/{verification_mode}','Client\ClientController@selfverification')->name('sendverificationlink');

Route::get('/admin/agents/allagents/telesale/update', [
	'uses' => 'Admin\TpvagentController@saleupdate'
	]
 )->name('tpvagents.sales.update');

Route::get('/admin/agents/allagents/telesale/leadupdate', [
        'uses' => 'Admin\TpvagentController@leadSaleUpdate'
    ]
)->name('tpvagents.lead.sales.update');

Route::get('/admin/agents/allagents/edit-profile', [
	'uses' => 'Admin\TpvagentController@editprofile'
	]
 )->name('tpvagents.edit-profile');

Route::post('/admin/agents/allagents/edit-profile', 'Admin\TpvagentController@updateprofile')->name('tpvagents.update-profile');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


/* Admin Controllers*/


Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function() {

	Route::any('/', function () {
		$user = \Auth::user();
		if($user->access_level == 'salesagent') {
			return redirect()->route('my-account');
		} else if($user->access_level == 'tpvagent') {
			return redirect()->route('tpvagents.sales');
		} else {
			if(Auth::user()->hasRole(['tpv_admin','tpv_qa']))
				{
					return redirect()->route("client.index");
				}
			else if(Auth::user()->access_level == 'salescenter')
			{
				return redirect()->route('dashboard',['type'=>base64_encode("salescenter"),'sid'=>base64_encode(Auth::user()->salescenter_id),'cid'=>base64_encode(Auth::user()->client_id)]);
			}
			else
			{
				return redirect()->route('dashboard');
			}
		}
	})->middleware(['auth']);
	Route::get('/dashboard',
	[
		'uses' =>'Admin\DashboardController@index'])->name('dashboard');
	Route::get('/agentdashboard',['uses' =>'Admin\AgentDashboardController@index'])->name('agentdashboard');

	// Get Agent Detail on agent dashboard route
	Route::get('/agent-details',['uses' =>'Admin\AgentDashboardController@getCounts'])->name('agentdashboard.get-agent-detail');
	
	//Get Agent Detail client wise on agent dashboard route
	Route::get('/agent-details-client-wise',['uses' =>'Admin\AgentDashboardController@getAgentDetailsClientWise'])->name('agentdashboard.get-agent-details-client-wise');

	Route::post('/dashboard/report', 'Admin\DashboardController@dashboardReport')->name('admin.dashboard.data');
	Route::post('/dashboard/leadData','Admin\DashboardController@leadDataReport')->name('admin.dashboard.leadDeclinedData');
	Route::post('/dashboard/verificationstatuschart', 'Admin\DashboardController@getVerificationStatusDonutChart')->name('admin.dashboard.verificationstatuschart');
	//Route::get('/dashboard/exportverificationstatusreport/{client_id}/{start_date}/{end_date}', 'Admin\DashboardController@exportVerificationStatusReport')->name('admin.dashboard.export.verificationstatusreport');
	Route::post('/dashboard/vendorsleadsbarchart', 'Admin\DashboardController@getVendorsLeadsBarChart')->name('admin.dashboard.vendorsleadsbarchart');
	Route::get('/dashboard/vendorsleadspiechart', 'Admin\DashboardController@getVendorsLeadsPieChart')->name('admin.dashboard.vendorsleadspiechart');
	Route::get('/dashboard/channelsleadsbarchart', 'Admin\DashboardController@getChannelsLeadsBarChart')->name('admin.dashboard.channelsleadsbarchart');
	Route::get('/dashboard/commodityleadsbarchart', 'Admin\DashboardController@getCommodityLeadsBarChart')->name('admin.dashboard.commodityleadsbarchart');
	Route::get('/dashboard/d2dpiechart', 'Admin\DashboardController@getD2dGoodSalesPieChart')->name('admin.dashboard.d2dgoodsalespiechart');
	Route::get('/dashboard/d2dBadpiechart', 'Admin\DashboardController@getD2dBadSalesPieChart')->name('admin.dashboard.d2dbadsalespiechart');
	Route::get('/dashboard/teleGoodpiechart', 'Admin\DashboardController@getTeleGoodSalesPieChart')->name('admin.dashboard.telegoodsalespiechart');
	Route::get('/dashboard/teleBadpiechart', 'Admin\DashboardController@getTeleBadSalesPieChart')->name('admin.dashboard.telebadsalespiechart');


	Route::get('/dashboard/textEmailCount','Admin\DashboardController@getTextEmailCount')->name('admin.dashboard.textEmailCount');
	Route::resource('roles','RoleController');
	Route::get('/get-all-permissions', [
		'middleware' => ['permission:view-user-roles'],
		'uses' => 'RoleController@getPermissions']
	)->name('all.permissions');
	Route::get('/edit-all-permissions-roles', [
		'middleware' => ['permission:edit-permission-roles'],
		'uses' => 'RoleController@editPermissionsRoles']
	)->name('edit.permissions.roles');

	Route::post('/update-all-permissions-roles', [
		'middleware' => ['permission:edit-permission-roles'],
		'uses' => 'RoleController@updatePermissionsRoles']
	)->name('update.permissions.roles');

	//client specific permissions

	Route::get('/all-external-permissions', [
		'middleware' => ['permission:view-user-roles'],
		'uses' => 'RoleController@getExternalPermissions']
	)->name('external.permissions');
	Route::get('/all-external-permissions/{client_id}', [
		'middleware' => ['permission:view-user-roles'],
		'uses' => 'RoleController@getClientExternalPermissions']
	)->name('all.external.permissions');
	Route::get('/edit-external-permissions-roles/{client_id}', [
		'middleware' => ['permission:edit-permission-roles'],
		'uses' => 'RoleController@getExternalPermissionsRoles']
	)->name('get.external.permissions.roles');
	Route::get('/edit-external-permissions-roles', [
		'middleware' => ['permission:edit-permission-roles'],
		'uses' => 'RoleController@editExternalPermissionsRoles']
	)->name('edit.external.permissions.roles');
	Route::post('/edit-external-permissions-roles', [
		'middleware' => ['permission:edit-permission-roles'],
		'uses' => 'RoleController@editExternalPermissionsRoles']
	)->name('edit.external.permissions.roles');

	Route::post('/update-external-permissions-roles', [
		'middleware' => ['permission:edit-permission-roles'],
		'uses' => 'RoleController@updateExternalPermissionsRoles']
	)->name('update.external.permissions.roles');

	Route::get('/incative-agents', [
		       'uses' => 'Admin\DashboardController@getinactivesalesagent']
	 )->name('getinactivesalesagents');
	Route::get('/test/{id}', [
		'uses' => 'Admin\DashboardController@testfunction']
	);

	 Route::any('/onboarding-agents', [
		'uses' => 'Admin\DashboardController@onboarding']
        )->name('onboarding');

    Route::get('tpv-recording', 'Calls\CallsController@getTpvRecording')->name('admin.tpv_recording.get')->middleware(['permission:generate-recordings-report']);
    Route::get('tpv-recording-ajax', 'Calls\CallsController@getTpvRecordingAjax')->name('admin.tpv_recording.ajax');
    //Route::get('tpv-recording/{id}/download', 'Calls\CallsController@downloadRecording')->name('admin.tpv_recording.download');
});

Route::group(['prefix' => 'admin', 'middleware' => 'isAuthenticated'], function() {
    // For new dashboard feature routes
    Route::post('dashboard/conversion-rate','Admin\DashboardController@getConversionRate')->name('dashboard.conversion-rate');
    Route::post('dashboard/load-client-logo','Admin\DashboardController@getClientLogo')->name('dashboard.client-logo');
    Route::post('dashboard/load-salescenter-donutchart','Admin\DashboardController@getSalesCenterWiseDonutChartData')->name('dashboard.load.salescenter.donut');
    Route::post('dashboard/load-salescenter-piechart','Admin\DashboardController@getSalesCenterWisePieChartData')->name('dashboard.load.salescenter.pie.data');
    Route::post('dashboard/load-client-piechart','Admin\DashboardController@getClientWisePieChartData')->name('dashboard.load.client.pie.data');
    Route::post('dashboard/load-salescenter-channel-data','Admin\DashboardController@getSalesCenterByChannelData')->name('dashboard.salescenter.channel.data');
    Route::post('dashboard/status-by-state','Admin\DashboardController@getStatusByState')->name('dashboard.status.by.state');
    Route::post('dashboard/load-salescenter-commodity-data','Admin\DashboardController@getSalesCenterByCommodityData')->name('dashboard.salescenter.commodity.data');
    Route::post('dashboard/load-leads-count-rate-data','Admin\DashboardController@getLeadsCountRateData')->name('dashboard.leads.count.rate.data');
    Route::post('dashboard/load-leads-count-verification-method','Admin\DashboardController@getLeadsCountByVerificationMethodData')->name('dashboard.leads.count.verification.method');
    Route::post('dashboard/top-performers','Admin\DashboardController@getTopPerformerData')->name('dashboard.top-performers');
    Route::post('dashboard/bottom-performers','Admin\DashboardController@getBottomPerformerData')->name('dashboard.bottom-performers');
    Route::post('dashboard/leads-by-sclocations','Admin\DashboardController@getLeadsBySalesCenterLocation')->name('dashboard.leads-by-sclocations');
    Route::post('dashboard/locations-wise-leads','Admin\DashboardController@getLocationsWiseLeads')->name('dashboard.locations-wise-leads');
    Route::post('dashboard/load-calender-pie-client-data','Admin\DashboardController@getCalenderPieClientData')->name('dashboard.calender.pie.client.data');
    Route::post('dashboard/leads-table-by-sclocations','Admin\DashboardController@GetLeadsTableBySalesCenterLocation')->name('dashboard.leads-status-table-by-sclocations');
    Route::post('dashboard/leads-by-commodity','Admin\DashboardController@getLocationsLeadsByCommodityData')->name('dashboard.leads-by-commodity');
	Route::post('dashboard/leads-by-salescenter-location-channel','Admin\DashboardController@getLeadsBySalesCenterLocationChannel')->name('dashboard.leads-by-salescenter-location-channel');
	Route::post('dashboard/top-programs','Admin\DashboardController@getTopProgramsBasedOnLeads')->name('dashboard.load.top.programs.donut');
	Route::post('dashboard/top-providers','Admin\DashboardController@getTopProvidersBasedOnLeads')->name('dashboard.load.top.providers.donut');
	Route::post('dashboard/state-wise-lead-map','Admin\DashboardController@getStateWiseLeadMap')->name('dashboard.load.state.wise.lead.map');
	
    Route::get('dashboard/map1','Admin\DashboardController@loadMapZipcode')->name('admin.dashboard.loadMap1');
    Route::get('dashboard/map2','Admin\DashboardController@loadMapSalesAgent')->name('admin.dashboard.loadMap2');
    Route::get('dashboard/telesalesleads', 'Admin\DashboardController@getTelesalesLeadsListByStatus')->name('admin.dashboard.telesalesleadslist');
    Route::get('dashboard/exportverificationstatusreport/{data?}', 'Admin\DashboardController@exportVerificationStatusReport')->name('admin.dashboard.export.verificationstatusreport');

	Route::get('dashboard/twilio-task-progress','Admin\AgentDashboardController@getTwilioLeadTaskReport')->name('agent.dashboard.twilio-task-progress');

	Route::get('dashboard/agent-activity-report','Admin\AgentDashboardController@agentActivityReport')->name('agent.dashboard.agent-activity-report');

	Route::get('dashboard/exportagentactivityreport/{data?}', 'Admin\AgentDashboardController@exportAgentActivityReport')->name('admin.dashboard.export.agentactivityreport');

});

Route::get('forms/{id}/tags', 'Client\FormsController@getAllTags')
    ->name('ajax.getFormTags');
Route::get('scripts/{id}/tags-category', 'Client\ScriptsController@getAllTagsWithCategory')
	->name('ajax.getTagesCategory');
Route::post('forms', 'Client\FormsController@getClientForms')
    ->name('ajax.getClientForms');

 //Route::middleware(['auth'])->group(['prefix' => 'admin/team'], function() {
Route::middleware(['auth'])->prefix( 'admin/team')->group( function() {
	Route::get('/edit-profile', 'Admin\DashboardController@editprofile')->name('edit-profile');
	Route::post('/edit-profile', 'Admin\DashboardController@updateprofile');
	Route::post('/remove-profile-photo', 'Admin\DashboardController@removeProfilePhoto')->name('removeProfilePhoto');
	Route::post('/edit-twiliosettings',[
		'uses' => 'Admin\DashboardController@updatetwiliosettings'
	 ])->name('edit-twiliosettings');

	 Route::get('/teammembers', [
	 	'middleware' => ['permission:all-users|view-tpv-users|add-tpv-users|edit-tpv-users'],
	    'uses' => 'Admin\TeamMemberController@index']
	   )->name('teammembers.index');

	 Route::post('/teammembers', [
	 	'as' => 'teammembers.store',
	 	'middleware' => ['permission:add-tpv-users|edit-tpv-users'],
	    'uses' => 'Admin\TeamMemberController@store']
	   );

	 Route::get('/teammembers/create',[
	 	// 'middleware' => ['permission:user-create'],
	 	'uses' => 'Admin\TeamMemberController@create'
	 	]
	  )->name('teammembers.create');

	 Route::get('/teammembers/{id}/edit', [
	 	// 'middleware' => ['permission:user-update'],
	 	'uses' => 'Admin\TeamMemberController@edit'
	 	]
	  )->name('teammembers.edit');

    Route::get('/teammembers/{id}', [
            // 'middleware' => ['permission:user-list'],
            'uses' => 'Admin\TeamMemberController@getTpvUser'
        ]
    )->name('teammembers.show');


    Route::get('/get-new-user', [
    		'middleware' => ['permission:view-tpv-users|edit-tpv-users'],
            'uses' => 'Admin\TeamMemberController@getTpvUser'
        ]
    )->name('admin.tpv.getTpvUser');



	  Route::get('/teammembers/{id}', [
	 	'middleware' => ['permission:user-list'],
	 	'uses' => 'Admin\TeamMemberController@show'
	 	]
	 )->name('teammembers.show');

	Route::match(['put', 'patch'], '/teammembers/{id}/update','Admin\TeamMemberController@update')->name('teammember.update');

	Route::post('/teammembers/delete', [
      	'middleware' => ['permission:user-delete'],
	 	'uses' => 'Admin\TeamMemberController@destroy'
	 	]
	  )->name('teammember.remove');


	Route::post('/teammembers/update-user-status', [
		 'uses' => 'Admin\TeamMemberController@changeUserStatus'
		]
	 )->name('team.user.changeUserStatus');


 });


 Route::middleware(['auth'])->prefix('admin/agents')->group( function() {
 /* TPV Agent */

 Route::post('/reasons', 'AgentPanel\TPVAgent\DispositionsController@dispositions')->name('tpvagents.retrieve-dispositions');
 Route::post('/reasons-for-admin', 'AgentPanel\TPVAgent\DispositionsController@dispositionsForAdmin')->name('tpvagents.retrieve-dispositions-for-admin');

 Route::post('/store-verified-reason', 'AgentPanel\TPVAgent\CallController@storeReason')->name('tpvagents.store-verified-reason');

 Route::get('/questions', 'Admin\TpvagentController@questions')->name('tpvagents.questions');
 Route::get('/get-tag-field', 'Admin\TpvagentController@getTagField')->name('tpvagents.getTagField');

 Route::post('/update-tag-field', 'Admin\TpvagentController@updateTagField')->name('tpvagents.updateTagField');

 Route::post('/reschedule-call', 'AgentPanel\TPVAgent\ScheduleCallController@rescheduleTask')->name('tpvagents.reschedule-call');

 Route::post('edit-filed', 'Admin\TpvagentController@getEditFiled')->name('get.field_question');
 Route::post('save-field-question', 'Admin\TpvagentController@saveFiledQuestion')->name('save.field_question');

 Route::get('/customer-questions', 'Admin\TpvagentController@customerQuestions')->name('tpvagents.customer.questions');
 Route::get('/call-hangup-details', 'Admin\TpvagentController@storeHangupDetails')->name('store.call.hangup.details');
 
 Route::get('/agent-not-found', 'Admin\TpvagentController@agentNotFoundQuestion')->name('tpvagents.agent.not.found');

     Route::get('/lead-not-found', 'Admin\TpvagentController@leadNotFoundQuestion')->name('tpvagents.lead.not.found');


 Route::post('/customer-lead-verify', 'Admin\TelesalesVerificationController@customerLeadVerify')->name('tpvagents.customer.lead.verify');


     Route::post('/save-customer-verification',[
         'uses' => 'Admin\TelesalesVerificationController@saveLeadUserAnswer'
     ])->name('tpvagents.save-customer-verification');



 Route::get('/dispositions', 'Admin\TpvagentController@dispositions')->name('tpvagents.dispositions');

 Route::get('/lead-decline', 'Admin\TpvagentController@leadDecline')->name('tpvagents.lead-decline');

 Route::get('/conform-decline', 'Admin\TpvagentController@conformDecline')->name('tpvagents.lead-conform-decline');

 Route::get('/allagents', [
	'middleware' => ['permission:view-all-agents|view-tpv-agents|add-tpv-agents|edit-tpv-agents'],
	'uses' => 'Admin\TpvagentController@index']
 )->name('tpvagents.index');

 Route::post('/allagents', [
	'as' => 'allagents.store',
	'middleware' => ['permission:add-tpv-agents|edit-tpv-agents'],
   	'uses' => 'Admin\TpvagentController@store']
  );
 Route::get('/create',[
	 'middleware' => ['permission:user-create'],
	 'uses' => 'Admin\TpvagentController@create'
	 ]
  )->name('tpvagents.create');

 
 /* Route::get('/{id}', [
	'middleware' => ['permission:user-list'],
	'uses' => 'Admin\TpvagentController@show'
	]
)->name('tpvagents.show');*/

    Route::get('/get-tpv-agent', [
     	'middleware' => ['permission:view-tpv-agents|edit-tpv-agents'],
        'uses' => 'Admin\TpvagentController@getTpvAgent'
        ]
    )->name('admin.tpv.gettpvagent');

    Route::get('/{id}/edit', [
	'middleware' => ['permission:user-update'],
	'uses' => 'Admin\TpvagentController@edit'
	]
	)->name('tpvagents.edit');
	
 Route::post( '/{id}/update',
 'Admin\TpvagentController@update')->name('tpvagents.update');

 Route::post('/delete', [
	'middleware' => ['permission:user-delete'],
   'uses' => 'Admin\TpvagentController@destroy'
   ]
)->name('tpvagents.remove');


Route::any('/allagents/tpvagent', [
	'uses' => 'Admin\TpvagentController@sales'
	]
 )->name('tpvagents.sales');

 Route::post('/allagents/findtelesale', [
	'uses' => 'Admin\TpvagentController@findsales'
	]
 )->name('tpvagents.findsales');



 Route::get('/support/dashboard', [
	'uses' => 'Admin\TpvagentController@supportdashboard'
	]
 )->name('tpvagent.support.dashboard');

});


/* Ajax routes to get Client Sales Agent  */
 Route::any('/client/agents', [
	'uses' => 'Admin\TpvagentController@getClientSalesAgents'
	]
 )->name('tpvagent.clientagents');
 Route::any('/client/agentsales', [
	'uses' => 'Admin\TpvagentController@getAgentSales'
	]
 )->name('tpvagent.agentsales');

 Route::any('/client/formscript', [
	'uses' => 'Admin\TpvagentController@getFormScript'
	]
 )->name('tpvagent.clientformscript');
 Route::any('/client/leadquestions', [
	'uses' => 'Admin\TpvagentController@getLeadQuestion'
	]
 )->name('tpvagent.leadquestions');

 Route::post('/client/createlead', [
	'uses' => 'Admin\TpvagentController@createlead'
	]
 )->name('tpvagent.createlead');


 

Route::middleware(['auth'])->prefix( 'admin/utilities')->group( function() {
	Route::get('/', [
    	 'uses' => 'Utility\UtilityController@index']
	 )->name('utilities.index');
	 Route::get('/{client_id}/add', [
		'uses' => 'Utility\UtilityController@addnew']
	)->name('client.utility.addnew');
	Route::post('/{client_id}/add', [
		'uses' => 'Utility\UtilityController@savenew']
	)->name('client.utility.addnew');
	Route::post('/{client_id}/store', [
		'middleware' => ['permission:edit-utility|add-utility-provider','isActiveClient'],
		'uses' => 'Utility\UtilityController@store']
	)->name('client.utility.store');
	Route::get('/view/{id}', [
		'uses' => 'Utility\UtilityController@viewutility']
	)->name('client.utility.view');
	Route::get('/edit', [
		'middleware' => ['permission:edit-utility|view-utility'],
		'uses' => 'Utility\UtilityController@edit']
	)->name('utility.edit');
	Route::get('/edit/{id}', [
		'uses' => 'Utility\UtilityController@editutility']
	)->name('client.utility.edit');
	Route::post('/edit/{id}', [
		'uses' => 'Utility\UtilityController@updateutility']
	)->name('client.utility.edit');
	Route::post('/{client_id}/store/validation', [
		'uses' => 'Utility\UtilityController@storeValidation']
	)->name('client.utility.store.validation');

	Route::post('/utility-validation/delete', [
		'uses' => 'Utility\UtilityController@deleteValidation']
	)->name('utility.validation.delete');

	Route::post('/utility-mapping/update', [
		'uses' => 'Utility\UtilityController@updateMapping']
	)->name('utility.mapping.update');

	Route::post('/delete', [
		'middleware' => ['permission:delete-utility'],
		'uses' => 'Utility\UtilityController@deleteutility']
	)->name('utility.delete');
  Route::get('/{client_id}/import-utility', [
   'uses' => 'Utility\UtilityController@importutility']
 )->name('client.utility.import');
 Route::post('/{client_id}/importparse', [
   'uses' => 'Utility\UtilityController@parseImport']
 )->name('client.utility.parseimport');
 Route::post('/{client_id}/importprocess', [
   'uses' => 'Utility\UtilityController@processImport']
 )->name('utility.import_process');
Route::get('/downloadSample', [
   'uses' => 'Utility\UtilityController@downloadSample']
 )->name('client.utility.downloadSample');
Route::get('/exportUtility/{client_id}', [
   'uses' => 'Utility\UtilityController@exportUtility']
 )->name('client.utility.exportUtility');
Route::get('/download-validation-sample', [
   'uses' => 'Utility\UtilityController@downloadValidationSample']
 )->name('client.utility.validation.downloadsample');
Route::get('/download-mapping-sample', [
   'uses' => 'Utility\UtilityController@downloadMappingSample']
 )->name('client.utility.mapping.downloadsample');

 Route::get('/{client_id}/bulkupload','Utility\UtilityController@bulkupload')->name('client.utility.bulkupload')->middleware(['permission:bulk-upload-utility']);
 Route::get('/{client_id}/bulkupload/validations','Utility\UtilityController@validationsBulkUpload')->name('client.utility.bulkupload.validations');
 Route::get('/{client_id}/bulkupload/mappings','Utility\UtilityController@mappingsBulkUpload')->name('client.utility.bulkupload.mappings');

 Route::get('/getvalidations','Utility\UtilityController@getValidationsList')->name('client.utility.getvalidations');
 
 Route::get('/getmappings','Utility\UtilityController@getMappingsList')->name('client.utility.getmappings');

Route::post('/import/{client_id}', [
	'middleware' => ['isActiveClient'],
   	'uses' => 'Utility\UtilityController@import']
 )->name('client.utility.importNew');

Route::post('/import/validation/{client_id}', [
   	'uses' => 'Utility\UtilityController@validationImport']
 )->name('client.utility.validation.import');
Route::post('/import/mapping/{client_id}', [
   	'uses' => 'Utility\UtilityController@mappingImport']
 )->name('client.utility.mapping.import');

 Route::post('/edit/{id}/utilitymapzipcode', [
	'uses' => 'Utility\ZipcodeController@mapUtility']
)->name('client.utility.mapzipcode');

 /* Utilities  compliance*/
 Route::get('/{client_id}/{utility_id}/list', [
	'uses' => 'Utility\ComplianceController@templates']
  )->name('client.utility.Compliances');
  Route::get('/{client_id}/{utility_id}/compliance/add', [
	'uses' => 'Utility\ComplianceController@addtemplate'
	]
   )->name('utility.compliance-add-templates');
   Route::post('/{client_id}/{utility_id}/compliance/add', [
	'uses' => 'Utility\ComplianceController@savetemplate'
	]
   );
   Route::get('/{client_id}/{utility_id}/compliance/edit/{id}', [
	'uses' => 'Utility\ComplianceController@edittemplate'
	]
   )->name('utility.compliance-edit-template');

   Route::post('/{client_id}/{utility_id}/compliance/edit/{id}', [
	'uses' => 'Utility\ComplianceController@updatetemplate'
	]
   );
   Route::post('/{client_id}/{utility_id}/compliance/delete', [
	'uses' => 'Utility\ComplianceController@deletetemplate'
	]
   )->name('utility.delete-compliance-template');

	/* Programs */
	Route::get('/programs', [
		'uses' => 'Programs\ProgramsController@index']
	)->name('utility.programs');
	Route::get('/programs/program-export/{client_id}', [
		'middleware' => ['permission:export-program'],
		'uses' => 'Programs\ProgramsController@exportProgram']
	)->name('utility.programs.exportProgram');
	Route::get('/programs/addnew', [
		'uses' => 'Programs\ProgramsController@addnewprogram']
	)->name('utility.programs.add');
	Route::post('/programs/import_parse', [
		'uses' => 'Programs\ProgramsController@parseImport']
	)->name('utility.programs.import_parse');
	Route::post('/programs/import_process', [
		'uses' => 'Programs\ProgramsController@processImport']
	)->name('utility.programs.import_process');
	Route::post('/programs/delete', [
		'uses' => 'Programs\ProgramsController@delete']
	)->name('utility.program.delete');

	// Edit Program route
	Route::post('/programs/edit', [
		'uses' => 'Programs\ProgramsController@edit']
	)->name('program.edit');
	

	Route::post('/programs/change-status', [
		'middleware' => ['permission:deactivate-program'],
		'uses' => 'Programs\ProgramsController@changeStatus']
	)->name('utility.program.changestatus');

	Route::put('/programs/store', [
		'middleware' => ['permission:add-program','isActiveClient'],
		'uses' => 'Programs\ProgramsController@store']
	)->name('utility.program.store');

	Route::get('/{client_id}/programs/bulkupload/', [
		'middleware' => ['permission:bulk-upload-program'],
		'uses' => 'Programs\ProgramsController@bulkUpload']
	)->name('utility.programs.bulkupload');

	Route::get('/programs/download-sample/{client_id}', [
		'uses' => 'Programs\ProgramsController@downloadSample']
	)->name('utility.programs.downloadSample');

	Route::post('/programs/import/{client_id}', [
		'middleware' => ['permission:bulk-upload-program','isActiveClient'],
		'uses' => 'Programs\ProgramsController@import']
	)->name('utility.programs.import');

	Route::get('/import-zipcode', [
		'uses' => 'Utility\ZipcodeController@index']
	)->name('utility.importzip');
	Route::post('/parse-zip-import', [
		'uses' => 'Utility\ZipcodeController@parseImport']
		)->name('client.utility.parsezipimport');

	  Route::post('/{client_id}/import-zip-process', [
		'uses' => 'Utility\ZipcodeController@processImport']
	  )->name('client.utility.import_zip_process');

		Route::get('/brand-contacts', [
			'uses' => 'Utility\BrandContactsController@index']
		)->name('utility.brandcontacts');
		Route::get('/brands/all/{client_id}','Utility\BrandContactsController@getBrands')->name('client.getBrands');
		Route::post('/brandcontact/savenewcontact', [
			'uses' => 'Utility\BrandContactsController@savenewcontact']
		)->name('brandcontact.savenewcontact');

		Route::get('/brandcontact/edit/{id}', [
			'uses' => 'Utility\BrandContactsController@editcontact']
		)->name('brandcontact.edit');
		Route::post('/brandcontact/edit/{id}', [
			'uses' => 'Utility\BrandContactsController@updatecontact']
		);

		Route::post('/brandcontact/deletecontact/', [
			'uses' => 'Utility\BrandContactsController@deletecontact']
		)->name('brandcontact.delete');





});

//Route::get('identify-create', function(){
//    Segment::identify([
//        "userId" => "12345abcde",
//        "traits" => [
//            "name"  => "James Brooks",
//            "email" => "test@test.com",
//        ]
//    ]);
//});
//
//Route::get('track-create', function(){
//    Segment::track([
//        "userId"     => "12345abcde",
//        "event"      => "Action Created",
//        "properties" => [
//            "was_awesome" => true,
//        ]
//    ]);
//});


//Route::group(['prefix' => 'admin/company', 'middleware' => ['auth']], function() {
	Route::middleware(['auth'])->prefix( 'admin/client')->group(function() {
	//'middleware' => ['permission:user-list'],
	//'middleware' => ['permission:user-create'],
	//'middleware' => ['permission:user-update'],
		// route use for bulk upload sales center user
		Route::middleware(['isActiveClient'])->prefix( 'salescenter')->group(function() {
			Route::get('/users/downloadsample/{client_id}', [
		   		'uses' => 'Salescenter\UserController@downloadSample']
		 	)->name('salescenter.user.downloadSample');

		 	Route::get('/{client_id}/users/bulkupload/{salescenter_id}', [
				'middleware' => ['permission:bulk-upload-sales-users'],
	 	   		'uses' => 'Salescenter\UserController@bulkUpload']
			)->name('salescenter.user.bulkupload');

			Route::post('/{client_id}/users/import/{salescenter_id}', [
				'middleware' => ['permission:bulk-upload-sales-users'],
			 	'uses' => 'Salescenter\UserController@import']
			)->name('salescenter.user.import');

			Route::get('/{client_id}/users/export/{salescenter_id}', [
				'middleware' => ['permission:export-sales-users'],
			 	'uses' => 'Salescenter\UserController@export']
			)->name('salescenter.user.export');
		});

        Route::get('/{id}/salescenters/downloadsample', [
	   		'uses' => 'Salescenter\SalescenterController@downloadSample']
	 	)->name('client.salesagents.downloadSample');

		Route::get('/{id}/forms/{formId}/scripts/{scriptId}/questions', 'Client\ScriptsController@questions')->name('admin.clients.forms.script.questions.index');

		Route::post('/forms/scripts/questions/condition', 'Client\ScriptsController@saveCondition')->name('clients.forms.script.questions.condition');

		Route::post('/questions/condition/retrive', 'Client\ScriptsController@getCondition')->name('retrive.script.questions.condition');

		Route::post('/retrive/nested/questions/', 'Client\ScriptsController@getQuestionsForCondition')->name('retrive.nested.script.questions.ajax');
		
		Route::post('/questions/condition/delete', 'Client\ScriptsController@deleteCondition')->name('delete.script.questions.condition');

        Route::get('/disposition/{id}', [
                'uses' => 'Disposition\DispositionsController@index'
            ]
        )->name('client.dispositionslist');

        Route::post('/', [
                'uses' => 'Disposition\DispositionsController@save'
            ]
        );

        Route::post('/disposition/store/{client_id}', [
        	'middleware' => ['permission:add-dispositions|edit-dispositions','isActiveClient'],
            'uses' => 'Disposition\DispositionsController@createOrUpdate'
            ]
        )->name('client.dispositioncreate');


        Route::get('/get-new-disposition/{client_id}', [
                'uses' => 'Disposition\DispositionsController@getDispositions'
            ]
        )->name('client.dispositionsdata');
        Route::get('/{id}/forms/{formId}/scripts/{scriptId}', 'Client\ScriptsController@show')->name('client.lead-forms.scripts.show');

        Route::post('/edit/{disposition_id}', [
                'uses' => 'Disposition\DispositionsController@update'
            ]
        );

        Route::get('/disposition/{client_id}/bulkupload', [
        		'middleware' => ['permission:bulk-upload-dispositions'],
                'uses' => 'Disposition\DispositionsController@bulkupload'
            ]
        )->name('disposition.bulkupload');

        Route::get('/disposition-download-sample', [
                'uses' => 'Disposition\DispositionsController@downloadSample'
            ]
        )->name('disposition.downloadsample');
        
        Route::post('/disposition/{client_id}/bulkupload', [
        		'middleware' => ['permission:bulk-upload-dispositions','isActiveClient'],
                'uses' => 'Disposition\DispositionsController@import'
            ]
        )->name('disposition.import');

        Route::get('/disposition/{client_id}/export', [
        		'middleware' => ['permission:export-dispositions'],
                'uses' => 'Disposition\DispositionsController@export'
            ]
        )->name('disposition.export');

		// Routes for doNotEnroll tab  :  Start
		Route::get('/do-not-enroll/{id}', [
				'uses' => 'Client\DoNotEnrollController@index'
			]
		)->name('do-not-enroll.index');

		Route::post('/do-not-enroll/store', [
        	'middleware' => ['permission:add-do-not-enroll','isActiveClient'],
            'uses' => 'Client\DoNotEnrollController@store'
            ]
        )->name('do-not-enroll.create');

		Route::post('/do-not-enroll/delete', [
			'middleware' => ['permission:delete-do-not-enroll'],
		   'uses' => 'Client\DoNotEnrollController@destroy'
		   ]
		)->name('do-not-enroll.delete');


		Route::get('/do-not-enroll/{client_id}/bulkupload', [
				// 'middleware' => ['permission:bulk-upload-dispositions'],
				'uses' => 'Client\DoNotEnrollController@DoNotEnrollBulkUpload'
			]
		)->name('do-not-enroll.bulkupload');

		Route::get('/donotenroll-download-sample', [
				'uses' => 'Client\DoNotEnrollController@downloadDoNotEnrollSampleSheet'
			]
		)->name('do-not-enroll.downloadsample');

		Route::post('/do-not-enroll/{client_id}/bulkupload', [
				'middleware' => ['permission:bulk-upload-do-not-enroll'],
				'uses' => 'Client\DoNotEnrollController@saveDNEBulkUpload'
			]
		)->name('do-not-enroll.import');
		
		Route::get('/do-not-enroll/{client_id}/export', [
			'middleware' => ['permission:export-do-not-enroll'],
				'uses' => 'Client\DoNotEnrollController@exportDNEList'
			]
		)->name('do-not-enroll.export');
		// End

	Route::post('/{id}/leadforms', 'Client\FormsController@store')->name('client.lead-forms.store')->middleware(['permission:add-new-form|edit-form']);
	Route::post('preview-leads-form', 'Client\FormsController@preview')->name('lead_forms.preview');
	Route::get('show-leads-form', 'Client\FormsController@show')->name('lead_forms.show');

	Route::post('/leadforms/change-status', [
		'uses' => 'Client\FormsController@changeStatus']
	)->name('leadforms.changestatus')->middleware(['permission:deactivate-form']);

	Route::get('/{id}/contact-form/create/field', 'Client\ClientController@field')->name('client.contact-page.field');

	//Route::get('/utility/{id}/programs', 'Programs\ProgramsController@get')

	Route::get('/salescenters/{client_id}', [
	 	   'uses' => 'Salescenter\SalescenterController@index']
	)->name('client.salescenters.index');

	Route::get('/{id}/salescenters/create', [
		'middleware' => ['permission:add-sales-center'],
	 	'uses' => 'Salescenter\SalescenterController@create']
	)->name('client.salescenters.create');

	Route::get('/{id}/salescenters/{sid}', [
		'middleware' => ['permission:edit-sales-center'],
	 	'uses' => 'Salescenter\SalescenterController@edit']
	)->name('client.salescenters.edit');

	Route::get('/{client_id}/salescenters/{sid}/bulkupload', [
	 	   'uses' => 'Salescenter\SalescenterController@bulkUpload']
	)->name('client.salesagents.bulkupload');

	Route::post('/{client_id}/salescenters/{sid}/import-agents', [
	 	   'uses' => 'Salescenter\SalescenterController@import']
	)->name('client.salesagents.importAgents');

	Route::get('/{client_id}/salescenters/{sid}/export-agents', [
	 	   'uses' => 'Salescenter\SalescenterController@exportAgnets']
	)->name('client.salesagents.exportAgents');
	Route::post('/salescenters/brands', [
		'uses' => 'Salescenter\SalescenterController@saveBrands']
)->name('client.salescenters.brands');

	//Route::get('/{id}/salescenters/{sid}/show', [
	// 	   'uses' => 'Salescenter\SalescenterController@show']
	//)->name('client.salescenters.show');

	Route::post('/{id}/salescenters/{sid}', [
		'middleware' => ['permission:edit-sales-center'],
	 	'uses' => 'Salescenter\SalescenterController@update']
	)->name('client.salescenters.update');

	Route::post('/{id}/check-sales-center-code', [
	 	   'uses' => 'Salescenter\SalescenterController@checkCode']
	)->name('client.sales-centers.check-code');

	Route::post('/check-sales-center-location-code', [
	 	   'uses' => 'Salescenter\SalescenterController@checkLocationCode']
	)->name('client.sales-centers-locations.check-code');

	Route::post('{client_id}/salescenters/{salecenter_id}', [
			'uses' => 'Salescenter\SalescenterController@exportSalesCenter']
	)->name('client.salescenters.export');


	Route::get('/list', [
		'middleware' => ['permission:all-clients'],
		'uses' => 'Client\ClientController@index']
	)->name('client.index');

	 Route::post('/', [
	 	'as' => 'client.store',
	 	'uses' => 'Client\ClientController@store']
	   );

	 Route::get('/create',[
	 		'uses' => 'Client\ClientController@create'
	 	]
	  )->name('client.create');

    Route::post('/check-client-id',[
            'uses' => 'Client\ClientController@checkClientId'
        ]
    )->name('client.check-client-id');

    Route::get('/check-client-code',[
            'uses' => 'Client\ClientController@checkClientCode'
        ]
    )->name('client.check-client-code');

	  Route::any('/update', [
	 	'uses' => 'Client\ClientController@update'
		]
	)->name('client.update');

	  Route::any('/updatenew', [
	 	'uses' => 'Client\ClientController@updateNew'
		]
	)->name('client.updateNew');
	 Route::get('/{id}/edit', [
	 	'middleware' => ['permission:edit-client-info'],
	 	'uses' => 'Client\ClientController@edit'
	 	]
	  )->name('client.edit');

	  Route::get('/{id}/show', [
	  	'middleware' => ['permission:view-client-info'],
		'uses' => 'Client\ClientController@show'
		]
	 )->name('client.show');

	 //Display Fraud-Alert Page
	 Route::get('/', [
		'uses' => 'Admin\FraudAlertController@index'
		]
	 )->name('admin.fruadalert');
	 //Fraud-Alert store 
	 Route::post('/alerts/store',[
		'uses' => 'Admin\FraudAlertController@store']
		)->name('admin.fruadalert.store');

	Route::post('/fraudalert',[
		'uses' => 'Admin\FraudAlertController@destroy']
		)->name('fraudalert.destroy');
		
	 //Fetch Alert_Level Data Route
	 Route::post('/findClientSalesCenter','Admin\FraudAlertController@findSalesCenter')->name('findclientsalescenter');

	  Route::get('/{id}/workflows', [
	  	'middleware' => ['permission:view-workflow'],
		'uses' => 'Client\TwilioController@workflows'
		]
	 )->name('client.workflow.index');

	  Route::get('/edit', [
		'middleware' => ['permission:edit-workflow'],
		'uses' => 'Client\TwilioController@editWorkflow']
		)->name('clients.workflow.edit');



	  Route::post('/statusupdate', [
		'uses' => 'Client\ClientController@statusupdate'
		]
	 )->name('client.statusupdate');

	 Route::get('/{id}/contact-page-layout/{formid}', [
	 	'middleware' => ['permission:edit-form'],
		'uses' => 'Client\ClientController@contactpagelayout'
		]
	 )->name('client.contact-page-layout');

	 Route::get('/{id}/contact-page-layout-clone/{formid}', [
	 	'middleware' => ['permission:edit-form'],
		'uses' => 'Client\ClientController@contactpagelayoutClone'
		]
	 )->name('client.contact-page-layout.clone');

	 Route::post('/{id}/contact-page-layout/{formid}', [
		'uses' => 'Client\ClientController@updatecontactpagelayout'
		]
	 );
      Route::get('/{id}/contact-form/create', [
      	'middleware' => ['permission:add-new-form'],
		'uses' => 'Client\ClientController@contactpagecreate'
		]
	 )->name('client.create-contact-page');
	 Route::post('/{id}/contact-form/create', [
		'uses' => 'Client\ClientController@savecontactpagelayout'
		]
	 );
	 Route::post('/{id}/contact-form/delete', [
		'uses' => 'Client\ClientController@deletecontactform'
		]
	 )->name('client.delete-contact-form');

	 Route::get('/{id}/forms/', [
		'uses' => 'Client\ClientController@contactforms'
		]
	 )->name('client.contact-forms');
	// Route::post('/lead-form/{id}', [
	// 	'middleware' => ['permission:delete-form'],
	// 	'uses' => 'Client\ClientController@deleteLeadForm'
	// 	]
	//  )->name('client.deleteLeadForm');
		 /* Form Scripts */

        Route::get('/{client_id}/forms/{form_id}/scripts', [
        	'middleware' => ['permission:view-scripts'],
            'uses' => 'Client\ScriptsController@list'
		])->name('admin.clients.scripts.index');

		Route::get('/{client_id}/forms/{form_id}/scripts/import/{script_upload_id}', [
            'uses' => 'Client\ScriptsController@getImportQuestions'
		])->name('admin.clients.import.question')->middleware('permission:upload-scripts');

        Route::post('/scripts/delete', [
            'uses' => 'Client\ScriptsController@delete'
        ])->name('scripts.delete');

		// Route::get('scripts/{id}/tags-category', 'Client\ScriptsController@getAllTagsWithCategory')
    	// 		->name('ajax.getTagesCategory');
        Route::get('/{client_id}/forms/{form_id}/export-tags/{script_tag}','Client\ScriptsController@getExportTags')->name('export.tags');

		Route::get('/download-sample-file/{clientid}/{formid}/{script}/{language?}/{state?}','Client\ScriptsController@downloadSampleFileScript')->name('download.sample.file');
		Route::get('/download-sample/{script}/{upload_type}','Client\ScriptsController@downloadSampleScript')->name('download.sample');
		Route::get('/check-state-script','Client\ScriptsController@checkStateScript')->name('check-state-script');
        /*
         * TODO: this is temp
         * */


		Route::post('import/script-question', 'Client\ScriptsController@importQuestions')->name('import.script.que')->middleware('permission:upload-scripts');
		Route::get('get/state', 'Client\ScriptsController@getAllStates')->name('get.state');
        Route::get('/{client_id}/forms/{form_id}/scripts-list', [
		'uses' => 'Client\FormScriptsController@index'
		]
	 )->name('client.contact-forms-scripts-langauge');

	 Route::get('/{client_id}/forms/{form_id}/scripts/{language}', [
		'uses' => 'Client\FormScriptsController@scriptsList'
		]
	 )->name('client.contact-forms-scripts');

	 Route::get('/{client_id}/forms/{form_id}/{language}/new-scripts', [
		'uses' => 'Client\FormScriptsController@newScript'
		]
	 )->name('client.contact-forms-new-scripts');

	 Route::post('/{client_id}/forms/{form_id}/{language}/new-scripts', [
		'uses' => 'Client\FormScriptsController@saveScript'
		]
	 );
	 Route::get('/{client_id}/forms/{form_id}/edit-scripts/{script_id}', [
		'uses' => 'Client\FormScriptsController@editScript'
		]
	)->name('client.edit-forms-script');

	Route::post('/{client_id}/forms/{form_id}/edit-scripts/{script_id}', [
		'uses' => 'Client\FormScriptsController@updateScript'
		]
	 );

	 Route::post('/{client_id}/forms/{form_id}/delete-scripts/', [
		'uses' => 'Client\FormScriptsController@deleteScript'
		]
	 )->name('client.delete-forms-script');

	/* End Form Scripts */

	/* Script questions  */
	Route::get('/{client_id}/{form_id}/{script_id}/questions', [
		'uses' => 'Client\ScriptQuestionController@questionsList'
		]
	 )->name('client.view-script-questions');

	 Route::get('/{client_id}/{form_id}/{script_id}/add-questions', [
		'uses' => 'Client\ScriptQuestionController@addQuestion'
		]
	 )->name('client.add-script-questions');

	 Route::post('/{client_id}/{form_id}/{script_id}/add-questions', [
		'uses' => 'Client\ScriptQuestionController@saveQuestion'
		]
	 );
	 Route::post('/{client_id}/{form_id}/{script_id}/delete-questions', [
		'uses' => 'Client\ScriptQuestionController@deleteQuestion'
		]
	 )->name('client.delete-script-question');

	 Route::post('/{client_id}/{form_id}/{script_id}/clone-questions', [
		'uses' => 'Client\ScriptQuestionController@cloneQuestion'
		]
	 )->name('client.clone-script-question');

	 Route::get('/{client_id}/{form_id}/{script_id}/{question_id}/edit-question', [
		'uses' => 'Client\ScriptQuestionController@editQuestion'
		]
	 )->name('client.edit-script-question');

	 Route::post('/{client_id}/{form_id}/{script_id}/{question_id}/edit-question', [
		'uses' => 'Client\ScriptQuestionController@updateQuestion'
		]
	 );
	 Route::post('/{client_id}/{form_id}/{script_id}/update-position', [
		'uses' => 'Client\ScriptQuestionController@updatePositions'
		]
	 )->name('client.update-question-positions');

	 Route::get('/{client_id}/scripts/create', function() {
		 return view('client.scripts.index');
	 })->name('admin.clients.scripts.create');

	 Route::get('/{client_id}/scripts/review', function() {
		return view('client.scripts.review');
	})->name('admin.clients.scripts.review');


	/* end Script questions  */


	 Route::get('/{id}/tele-sales', [
		'uses' => 'Client\ClientController@leads'
		]
	 )->name('client.contact-leads');

	 Route::get('/findsalecenter', [
		'uses' => 'Client\ClientController@findsalecenter'
		]
	 )->name('client.findsalecenter');
	 Route::get('/findsalesagents', [
		'uses' => 'Client\ClientController@findsalesagents'
		]
	 )->name('client.findsalesagents');

/* compliance code */
Route::get('/{client_id}/compliance', [
 'uses' => 'Client\ComplianceController@templates'
 ]
)->name('client.compliance-templates');
Route::get('/{client_id}/compliance/add', [
 'uses' => 'Client\ComplianceController@addtemplate'
 ]
)->name('client.compliance-add-templates');
Route::post('/{client_id}/compliance/add', [
 'uses' => 'Client\ComplianceController@savetemplate'
 ]
);

Route::get('/{client_id}/compliance/edit/{id}', [
 'uses' => 'Client\ComplianceController@edittemplate'
 ]
)->name('client.compliance-edit-template');

Route::post('/{client_id}/compliance/delete', [
 'uses' => 'Client\ComplianceController@deletetemplate'
 ]
)->name('client.delete-compliance-template');
Route::post('/{client_id}/compliance/edit/{id}', [
 'uses' => 'Client\ComplianceController@updatetemplate'
 ]
);
Route::get('/{client_id}/compliance-reports', [
 'uses' => 'Client\ComplianceReportingController@index'
 ]
)->name('client.compliance-reports');
Route::get('/{client_id}/compliance-reports-export', [
 'uses' => 'Client\ComplianceReportingController@export'
 ]
)->name('client.compliance-reports-export');
Route::get('/{client_id}/compliance-reports-export-all', [
 'uses' => 'Client\ComplianceReportingController@exportall'
 ]
)->name('client.compliance-reports-export-all');




	/* Client users */

	Route::get('/{id}/add-user', [
		 'uses' => 'Client\ClientController@createuser'
		]
	 )->name('client.createuser');

	 Route::match(['put', 'post'], '/{id}/storeuser','Client\ClientController@storeuser')->name('client.storeuser');

	 Route::get('/{id}/users', [
			'uses' => 'Client\ClientController@users'
		]
	 )->name('client.users');
	Route::get('/users-new/{id}', [
			'uses' => 'Client\ClientController@usersNew'
		]
	)->name('client.usersNew');

    Route::get('/client-users', [
    		'middleware' => ['permission:view-client-user'],
            'uses' => 'Client\ClientController@usersNew'
        ]
    )->name('admin.client-users');


	Route::get('/get-new-user/{client_id}', [
			'middleware' => ['permission:view-client-user|edit-client-user'],
			'uses' => 'Client\ClientController@getUser'
		]
	)->name('client.getUser');

        Route::get('/get-user', [
        		'middleware' => ['permission:view-client-user|edit-client-user'],
                'uses' => 'Client\ClientController@getUser'
            ]
        )->name('admin.client.getUsers');


	Route::post('/users-new/store/{client_id}', [
			'middleware' => ['permission:add-client-user|edit-client-user','isActiveClient'],
			'uses' => 'Client\ClientController@createOrUpdateUser'
		]
	)->name('client.user.createOrUpdate');

        Route::post('/users-new/store', [
        		'middleware' => ['permission:add-client-user|edit-client-user','isActiveClient'],
                'uses' => 'Client\ClientController@createOrUpdateUser'
            ]
        )->name('admin.client.users.StoreOrEdit');


	 Route::get('/{id}/user/{userid}/show', [
		'uses' => 'Client\ClientController@showuser'
		]
	 )->name('client.user.show');

	 Route::get('/{id}/user/{userid}/edit', [
		'uses' => 'Client\ClientController@edituser'
		]
	 )->name('client.user.edit');

	 Route::post('/{id}/user/{userid}/edit', [
		'as' => 'client.edituser',
		'uses' => 'Client\ClientController@updateuser'
		]
	 )->name('client.user.edit');

 	 Route::post('/{id}/user/update', [
		 'uses' => 'Client\ClientController@updateuserstatus'
		]
	 )->name('client.user.update');


 	Route::post('/user/update-user-status', [
 		'middleware' => ['permission:deactivate-client-user|delete-tpv-users|delete-tpv-agents'],
		'uses' => 'Client\ClientController@changeUserStatus'
		]
	 )->name('client.user.changeUserStatus');


	/* Client Sales centers */
	Route::get('/{id}/salescenters', [
		  'uses' => 'Client\ClientController@salescenters'
		]
	 )->name('client.salescenters');


	Route::get('/{id}/add-salecenter', [
		 'uses' => 'Salescenter\SalescenterController@create'
		]
	 )->name('client.createsalescenter');

	 Route::post('/{id}/salescenters', [
		'uses' => 'Salescenter\SalescenterController@store'
		]
	 )->name('client.salescenter.store');

	 Route::post('new/{id}/salescenters', [
	 	'middleware' => ['permission:add-sales-center'],
		'uses' => 'Salescenter\SalescenterController@storeNew'
		]
	 )->name('client.salescenter.storeNew');

	 Route::get('/{id}/salescenter/{salescenter_id}/edit', [
		'uses' => 'Salescenter\SalescenterController@edit'
		]
	 )->name('client.salescenter.edit');

	 Route::post('/{id}/salescenter/{salescenter_id}/edit', [
		'uses' => 'Salescenter\SalescenterController@update'
		]
	 )->name('client.salescenter.edit');

	 Route::get('/{id}/salescenter/{salescenter_id}/show', [
	 	'middleware' => ['permission:view-sales-center'],
		'uses' => 'Salescenter\SalescenterController@show'
		]
	 )->name('client.salescenter.show');
	 Route::post('/{id}/salescenter/delete', [
	 	'middleware' => ['permission:deactivate-sales-center'],
		'uses' => 'Salescenter\SalescenterController@delete'
	   ]
	)->name('client.salescenter.delete');

	 /* Client -> salescenter -> user */

	 Route::get('/{client_id}/salescenter/{salescenter_id}/users', [
		'uses' => 'Salescenter\SalescenterController@salescenterusers'
		]
	 )->name('client.salescenter.users');

	Route::get('/{client_id}/salescenter/{salescenter_id}/user-list', [
		'uses' => 'Salescenter\SalescenterController@userList'
		]
	)->name('salescenter.users.index');



	 Route::get('/{client_id}/salescenter/{salescenter_id}/add-user', [
			'uses' => 'Salescenter\SalescenterController@adduser'
		]
	 )->name('client.salescenter.adduser');
	Route::post('/{client_id}/salescenter/{salescenter_id}/add-user', [
		'uses' => 'Salescenter\SalescenterController@saveuser'
	]);
	Route::post('/client/salescenter/create-update-user', [
		'middleware' => ['permission:add-sales-users|edit-sales-users','isActiveClient'],
		'uses' => 'Salescenter\SalescenterController@createOrUpdateUser'
	])->name('salescenter.users.createOrUpdate');

	Route::post('/salescenter/change-user-status', [
		'middleware' => ['permission:deactivate-sc-admin|deactivate-sc-qa|deactivate-sc-location-admin'],
		'uses' => 'Salescenter\SalescenterController@changeUserStatus'
	])->name('salescenter.users.changeUserStatus');

	Route::get('/{client_id}/salescenter/{salescenter_id}/get-locations', [
		'uses' => 'Salescenter\SalescenterController@getLocations'
		]
	)->name('salescenter.location.index');
    Route::post('/{client_id}/salescenter/{salescenter_id}/locations-change-status', [
            'uses' => 'Salescenter\SalescenterController@locationChangeStatus'
        ]
    )->name('salescenters.locations.change-status');
    Route::post('salescenter/location/delete', [
    		'middleware' => ['permission:delete-sales-center-location'],
            'uses' => 'Salescenter\SalescenterController@deleteLocation'
        ]
    )->name('salescenters.locations.delete');
	Route::get('/salescenter/show-location', [
		'uses' => 'Salescenter\SalescenterController@showLocation'
		]
	)->name('salescenter.location.show');
	Route::get('/salescenter/locations/{client_id?}/{salescenter_id?}', [
		'uses' => 'Salescenter\SalescenterController@getSalesCenterLocations'
		]
	)->name('salescenter.getSalesCenterLocations');
	Route::post('/{client_id}/salescenter/{salescenter_id}/create-update-location', [
		'middleware' => ['isActiveClient'],
		'uses' => 'Salescenter\SalescenterController@createOrUpdateLocation'
	])->name('salescenter.location.createOrUpdate');


	Route::get('/{client_id}/salescenter/{salescenter_id}/user/{userid}/edit', [
		'uses' => 'Salescenter\SalescenterController@edituser'
		]
	 )->name('client.salescenter.user.edit');

	 Route::post('/{client_id}/salescenter/{salescenter_id}/user/{userid}/edit', [
		'uses' => 'Salescenter\SalescenterController@updateuser'
		]
	 )->name('client.salescenter.user.edit');

	 Route::get('/{client_id}/salescenter/{salescenter_id}/user/{userid}/show', [
		'uses' => 'Salescenter\SalescenterController@showuser'
		]
	 )->name('client.salescenter.user.show');

	 Route::post('/{client_id}/salescenter/{salescenter_id}/user/updatestatus', [
		'uses' => 'Salescenter\SalescenterController@updatestatus'
	   ]
	)->name('client.salescenter.user.updatestatus');


	/* Sales centers locations  */
   Route::get('/{client_id}/salescenter/{salescenter_id}/locations', [
		'uses' => 'Salescenter\SalescenterController@locations'
		]
	 )->name('client.salescenter.locations');

	 Route::get('/{client_id}/salescenter/{salescenter_id}/add-location', [
		'uses' => 'Salescenter\SalescenterController@addlocation'
		]
	 )->name('client.salescenter.addlocation');

	 Route::post('/{client_id}/salescenter/{salescenter_id}/add-location', [
		'uses' => 'Salescenter\SalescenterController@savelocation'
		]
	 );
	 Route::get('/{client_id}/salescenter/{salescenter_id}/location/{location_id}/edit', [
		'uses' => 'Salescenter\SalescenterController@editlocation'
		]
	 )->name('client.salescenter.location.edit');

	 Route::post('/{client_id}/salescenter/{salescenter_id}/location/{location_id}/edit', [
		'uses' => 'Salescenter\SalescenterController@updatelocation'
		]
	 );






	 /* sales center  sales agents */

	 Route::get('/{client_id}/salesagents/bulkupload', function() {
		 return view('client.salescenter.salesagent.bulkupload');
	 }
	 )->name('client.sales-agents.bulk-upload');

	 Route::get('/{client_id}/salescenter/{salescenter_id}/salesagents', [
		'uses' => 'Salesagent\SalesagentController@salesagents'
		]
	 )->name('client.salescenter.salesagents');

	 Route::get('/{client_id}/salescenter/{salescenter_id}/add-agent', [
		'uses' => 'Salesagent\SalesagentController@adduser'
	 ])->name('client.salescenter.addsalesagent');

	 Route::post('/{client_id}/salescenter/{salescenter_id}/add-agent', [
		'uses' => 'Salesagent\SalesagentController@saveuser'
	]);
	Route::get('/{client_id}/salescenter/{salescenter_id}/salesagent/{userid}/show', [
		'uses' => 'Salesagent\SalesagentController@showuser'
		]
	 )->name('client.salescenter.salesagent.show');
	 Route::get('/{client_id}/salescenter/{salescenter_id}/salesagent/{userid}/edit', [
		'uses' => 'Salesagent\SalesagentController@edituser'
		]
	 )->name('client.salescenter.salesagent.edit');

	 Route::post('/{client_id}/salescenter/{salescenter_id}/salesagent/{userid}/edit', [
		'uses' => 'Salesagent\SalesagentController@updateuser'
		]
	 )->name('client.salescenter.salesagent.edit');

	 Route::get('/salescenter/salesagent/edit', [
	 	'middleware' => ['permission:view-sales-agents|edit-sales-agents'],
		'uses' => 'Salesagent\SalesagentController@edit'
		]
	 )->name('salesagent.edit');
	 Route::post('/salescenter/salesagent/changeUserStatus', [
		'uses' => 'Salesagent\SalesagentController@changeUserStatus'
		]
	 )->name('agent.user.changeUserStatus');

	 Route::post('/salescenter/salesagent/changeUserStatus-for-all-agent', [
		'uses' => 'Salesagent\SalesagentController@changeUserStatus'
		]
	 )->name('agent.user.changeUserStatusForAllAgent');

	 Route::post('/{client_id}/salescenter/{salescenter_id}/salesagent/{userid}/editdetail', [
		'uses' => 'Salesagent\SalesagentController@updateuserdetail'
		]
	 )->name('client.salescenter.salesagent.editdetail');

	 Route::post('/{client_id}/salescenter/{salescenter_id}/salesagent/{userid}/deletefile', [
		'uses' => 'Salesagent\SalesagentController@deleteFile'
		]
	 )->name('client.salescenter.salesagent.deletefile');


	 Route::post('/{client_id}/salescenter/salesagent/updatestatus', [
		'uses' => 'Salesagent\SalesagentController@updatestatus'
	   ]
	)->name('client.salescenter.salesagent.updatestatus');

	Route::post('/save', [
		'middleware' => ['permission:add-sales-agents|edit-sales-agents','isActiveClient'],
		'uses' => 'Salesagent\SalesagentController@save'
	   	]
	)->name('salesagent.save');

	Route::post('/delete-document/{id}', [
		'uses' => 'Salesagent\SalesagentController@deleteDocuments'
	   ]
	)->name('salesagent.deleteDocuments');

	Route::post('/show-document/{id}', [
		'uses' => 'Salesagent\SalesagentController@showDocuments'
	   ]
	)->name('salesagent.showDocuments');

	 /*  Import Lead Data*/

	 Route::any('/{client_id}/leadimport', [
		'uses' => 'Client\ImportLeadsController@index'
	   ]
	)->name('client.importlead');

	 Route::any('/{client_id}/mapleadfields', [
		'uses' => 'Client\ImportLeadsController@mapleadfields'
	   ]
	)->name('client.mapleadfields');

	 Route::post('/{client_id}/import-lead-data', [
		'uses' => 'Client\ImportLeadsController@processImport']
	)->name('client.importleaddata');

	Route::get('/get-sales-agents-by-location', [
		'uses' => 'Salesagent\SalesagentController@getSalesCenterAgentsOptionByLocation'
	   ]
	)->name('ajax.getSalesCenterAgentsOption');
 });

 /* Admin Report pages */
 Route::middleware(['auth'])->prefix( 'admin/report')->group(function() {
	// Route::get('/', [
	// 	'middleware' => ['permission:generate-enrollment-report'],
	// 	'uses' => 'Reports\ReportsController@index'
	// 	]
	//  )->name('reports.reportform');

	 Route::get('/office-report', [
		'uses' => 'Reports\ReportsController@officereport'
		]
	 )->name('reports.officereport');

	 Route::get('/results', [
		'uses' => 'Reports\ReportsController@results'
		]
	 )->name('reports.action');
	 Route::get('/batchreport', [
		'uses' => 'Reports\ReportsController@batchreport'
		]
	 )->name('reports.batchreport');
	 Route::get('/batchexport', [
		'uses' => 'Reports\ReportsController@exportbatch'
		]
	 )->name('report.exportbatch');
	 Route::get('/batchexportall', [
		'uses' => 'Reports\ReportsController@batchexportall'
		]
	 )->name('report.batchexportall');

	 Route::get('/export', [
		'uses' => 'Reports\ReportsController@exportresults'
		]
	 )->name('reports.exports');

     Route::get('/enrollment-report-export', [
             'uses' => 'Reports\ReportsController@exportEnrollmentReport'
         ]
     )->name('reports.exports.enrollment');

     Route::get('/sales-report-export', [
             'uses' => 'Reports\ReportsController@exportSalesReport'
         ]
     )->name('reports.exports.daily');

	 Route::get('/inactiveSalesagentsExports', [
		'uses' => 'Reports\ReportsController@inactiveSalesagentsExports'
		]
	 )->name('reports.inactiveSalesagentsExports');

	 Route::get('/state-training', [
		'uses' => 'Reports\ReportsController@statetraining'
		]
	 )->name('reports.statetraining');
	 Route::get('/state-training-export', [
		'uses' => 'Reports\ReportsController@exportstateresults'
		]
	 )->name('reports.statetrainingexport');
	 Route::get('/program-report', [
		'uses' => 'Reports\ReportsController@programreport'
		]
	 )->name('reports.programreport');
	 Route::get('/program-export', [
		'uses' => 'Reports\ReportsController@programexport'
		]
	 )->name('reports.programexport');


	 Route::get('/sales-report', [
	 	'middleware' => ['permission:generate-enrollment-report'],
		'uses' => 'Reports\ReportsController@salesreport'
		]
	 )->name('reports.salesreportform');


	Route::get('/sales-activity', [
		'middleware' => ['permission:generate-sales-activity-report'],
		'uses' => 'Reports\ReportsController@salesActivity'
		]
	 )->name('reports.sales.activity');

	Route::get('/critical-alert', [
		'middleware' => ['permission:generate-critical-alert-report'],
		'uses' => 'Reports\ReportsController@criticalReport'
		]
	)->name('reports.critical.alert');

	// Call history Report
	Route::get('/call-history', [
		'uses' => 'Reports\ReportsController@callHistoryReport'
		]
	)->name('reports.call.history');

	Route::post('/critical-alert/export', [
		'middleware' => ['permission:export-critical-alert-report'],
		'uses' => 'Reports\ReportsController@exportCriticalReport'
		]
	)->name('export.critical.alert');


	Route::get('/show-sales-agent-trail-report', [
		'middleware' => ['permission:generate-sales-agent-trail'],
		'uses' => 'Reports\ReportsController@showSalesAgentTrailReport'
		]
	)->name('show.sales.agent.trail');
	Route::get('/show-billing-report', [
		'middleware' => ['permission:generate-billing-report'],
		'uses' => 'Reports\BillingReportController@index'
		]
	)->name('show.billing.report');
	Route::post('/show-billing-report', [
		'middleware' => ['permission:generate-billing-report'],
		'uses' => 'Reports\BillingReportController@index'
		]
	)->name('show.billing.report');

	Route::get('/show-call-details-report', [
		'middleware' => ['permission:generate-call-detail-report'],
		'uses' => 'Reports\CallDetailsReportController@index'
		]
	)->name('show.calldetails.report');

	Route::get('/billing-export', [
		'uses' => 'Reports\BillingReportController@exportBillingReport'
	])->name('billing.export');
	
	Route::get('/calldetails-export', [
		'uses' => 'Reports\CallDetailsReportController@exportCallDetailsReport'
	])->name('calldetails.export');

	Route::get('/get-sales-agent-activity-locations', [
		'uses' => 'Reports\ReportsController@getSalesAgentActivityLocations'
		]
	)->name('getSalesAgentActivityLocations');
 });

 // Admin Reports new pages route
//  Route::middleware(['auth'])->prefix( 'admin/report')->group(function() {
// 	Route::get('/enrollment-report', [
// 		'middleware' => ['permission:generate-enrollment-report'],
// 		'uses' => 'Reports\EnrollmentReportController@index'
// 		]
// 	 )->name('reports.reportform');

// 	 Route::get('/enrollment-export', [
// 		'uses' => 'Reports\EnrollmentReportController@exportReport'
// 	])->name('enrollment.export');

//  });

 Route::middleware(['auth'])->prefix( 'admin/report')->group(function() {
	Route::get('/enrollment-report', [
		'middleware' => ['permission:generate-enrollment-report'],
		'uses' => 'Reports\EnrollmentReportController@index'
		]
	)->name('reports.reportform');
	Route::post('/enrollment-report', [
		'middleware' => ['permission:generate-enrollment-report'],
		'uses' => 'Reports\EnrollmentReportController@index'
		]
	)->name('reports.enrollment-report-data');
	Route::get('/enrollment-export', [
		'uses' => 'Reports\EnrollmentReportController@exportReport'
		]
	)->name('enrollment.export');
	Route::get('/enrollment-export/state-ajax', [
		'uses' => 'Reports\EnrollmentReportController@getStateAjax'
		]
	)->name('reports.ajax.state');
	Route::get('/enrollment-export/program-details', [
		'uses' => 'Reports\EnrollmentReportController@getProgramDetails'
		]
	)->name('reports.ajax.program-details');
	Route::post('/enrollment-export/brand-ajax', [
		'uses' => 'Reports\EnrollmentReportController@getBrandsAjax'
		]
	)->name('reports.ajax.brands');

	// Routes for Mega energy enrollment report (Daily Vaerified Calls Report)
	Route::get('/mega-enrollment-report', [
		// 'middleware' => ['permission:generate-enrollment-report'],
		'uses' => 'Reports\MegaEnrollmetReportController@index'
		]
	)->name('reports.megareportform');
	Route::post('/mega-enrollment-report', [
		// 'middleware' => ['permission:generate-enrollment-report'],
		'uses' => 'Reports\MegaEnrollmetReportController@index'
		]
	)->name('reports.mega-enrollment-report-data');
	Route::get('/mega-enrollment-export', [
		'uses' => 'Reports\MegaEnrollmetReportController@exportReport'
		]
	)->name('megaenrollment.export');

	Route::get('/ptm-enrollment-report', [
		// 'middleware' => ['permission:generate-enrollment-report'],
		'uses' => 'Reports\PTMEnrollmetReportController@index'
		]
	)->name('reports.ptmreportform');
	Route::post('/ptm-enrollment-report', [
		// 'middleware' => ['permission:generate-enrollment-report'],
		'uses' => 'Reports\PTMEnrollmetReportController@index'
		]
	)->name('reports.ptm-enrollment-report-data');
	Route::get('/ptm-enrollment-export', [
		'uses' => 'Reports\PTMEnrollmetReportController@exportReport'
	])->name('ptmenrollment.export');
	Route::get('/ptm-get-updates','Reports\PTMEnrollmetReportController@getSolarUpdates')->name('reports.ptm-enrollment-report.getupdates');

 });

  /* Admin Dispositions pages */
  Route::middleware(['auth'])->prefix( 'admin/dispositions')->group(function() {
	Route::get('/', [
		'uses' => 'Disposition\DispositionsController@index'
		]
	 )->name('admin.dispositionslist');
	 Route::post('/', [
		'uses' => 'Disposition\DispositionsController@save'
		]
	 );

	 Route::get('/create', [
		'uses' => 'Disposition\DispositionsController@create'
		]
	 )->name('admin.dispositioncreate');
	 Route::get('/edit/{disposition_id}', [
		'uses' => 'Disposition\DispositionsController@edit'
		]
	 )->name('admin.dispositionedit');

	 Route::post('/edit/{disposition_id}', [
		'uses' => 'Disposition\DispositionsController@update'
		]
	 );
	 Route::post('/delete', [
	 	'middleware' => ['permission:delete-dispositions'],
		'uses' => 'Disposition\DispositionsController@delete'
		]
	 )->name('admin.dispositiondelete');

      Route::post('/delete-disposition', [
              'middleware' => ['permission:delete-dispositions'],
              'uses' => 'Disposition\DispositionsController@activeInactiveDisposition'
          ]
      )->name('admin.disposition-delete-data');

 });

/* Front-end Login pages */

Route::middleware(['auth'])->group( function() {
/* Company contact form  */
Route::get('{id}/createlead',[
	'uses' => 'Client\ClientController@contactform'
 ], function ($id) {
	return abort(403);
})->name('client.contact');

Route::get('{id}/createlead/{form_id}',[
'uses' => 'Client\ClientController@designFrom'
])->name('client.contact.from');

Route::post('{id}/createlead/{form_id}',[
    'uses' => 'Client\ClientController@designFromPost'
])->name('client.contact.from_post');

Route::get('cancellead/{id}/{clientId}',[
    'uses' => 'Client\ClientController@cancelLead'
])->name('client.contact.cancel_lead');

Route::get('cancelnewLead/{id}',[
    'uses' => 'Client\ClientController@cancelnewLead'
])->name('client.cancelnewLead');

Route::get('proceedlead/{id}',[
    'uses' => 'Client\ClientController@proceedLead'
])->name('client.proceed_lead');


    Route::post('/client-get-utilities',[
        'uses' => 'Client\ClientController@getUtilities'
    ])->name('client.get.utilities');



Route::get('{id}/thank-you',[
	'uses' => 'Client\ClientController@contactthanks'
 ], function ($id) {
	return abort(403);
})->name('client.thank-you');

Route::post('{id}/createlead',[
	'uses' => 'Client\ClientController@actioncontact'
 ], function ($id) {
	return abort(403);
})->name('client.contactaction');

Route::post('validateLeadData',[
	'uses' => 'Client\ClientController@validateLeadData'
 ])->name('client.validateLeadData');





/* My account */
Route::any('/my-account',[
	'uses' => 'User\UserController@index'
 ])->name('my-account')->middleware('auth');
 Route::get('/edit-profile',[
	'uses' => 'User\UserController@editprofile'
 ])->name('editprofile');
 Route::post('/edit-profile',[
	'uses' => 'User\UserController@updateprofile'
 ]);
 Route::get('/sales-user-profile', 'User\UserController@getSalesUserProfile')->name('sales-user-profile');
 Route::post('/sales-user-profile',[
        'uses' => 'User\UserController@updatePassword'
    ])->name('update-salesagent-profile');

 Route::any('/my-leads',[
	'uses' => 'User\UserController@myleads'
 ])->name('profile.leads');

    Route::any('/my-leads-ajax',[
        'uses' => 'User\UserController@myLeadsAjax'
    ])->name('profile.leads.ajax');
 Route::get('/my-leads/{leadid}',[
	'uses' => 'User\UserController@leaddetail'
 ])->name('profile.leaddetail');

 	Route::post('/store/lead-self-verify/{lead_id}',[
        'uses' => 'Client\ClientController@selfverify'
    ])->name('store.selfverify');

 	/* Enrollment Form Validation */
    Route::post('/validate/lead/customerdetails',[
		'uses' => 'Client\ClientController@enrollmentFormBackendValidations'
	])->name('client.lead.validate.customer');

	/*add multienrollment form fields*/
	Route::post('/form/addfields',[
		'uses' => 'Client\ClientController@addFieldToEnrollmentForm'
	])->name('client.lead.add.fields');

});

/* Ajax functions  */
Route::post('/ajax/checkFormNameExist',[
    'uses' => 'Client\FormsController@checkFormNameExist'
])->name('ajax.checkFormNameExist');
Route::get('/ajax/getZipCodes',[
	'uses' => 'Utility\UtilityController@getZipCodes'
 ])->name('ajax.getZipCodes');
Route::get('/ajax/zipcode/',[
    'uses' => 'Utility\UtilityController@zipcodeSearch'
])->name('ajax.zipcodeSearch');
Route::get('/ajax/getUtilityByCommodity',[
	'uses' => 'Utility\UtilityController@getUtilityByCommodity'
 ])->name('ajax.getUtilityByCommodity');
Route::get('/ajax/getProviderByUtilityName',[
	'uses' => 'Utility\UtilityController@getProviderByUtilityName'
 ])->name('ajax.getProviderByUtilityName');
Route::get('/ajax/getMarketByProvider',[
	'uses' => 'Utility\UtilityController@getMarketByProvider'
 ])->name('ajax.getMarketByProvider');
Route::post('/getutility',[
	'uses' => 'Utility\UtilityController@getutility'
 ]);
 Route::post('/ajax/getClientUtility',[
	'uses' => 'Utility\UtilityController@getClientUtility'
 ])->name('compliance_report.getClientUtility');
 Route::post('/ajax/compliancetemplates',[
	'uses' => 'Utility\ComplianceController@ajaxComplianceTemplates'
 ]);
 Route::any('/ajax/getzipcode',[
	'uses' => 'Utility\ZipcodeController@ajaxzipcode'
 ]);
 Route::any('/ajax/validatezip',[
	'uses' => 'Utility\ZipcodeController@validatezip'
 ]);
 Route::any('/ajax/getprograms',[
	'uses' => 'Programs\ProgramsController@getProgramsFormUtility'
 ])->name('ajax.getprograms');

 Route::post('ajax/getsalescenterandcommodity', 'Salescenter\SalescenterController@getSalesCenterAndCommodity')->name('ajax.getSalesCenterAndCommodity');

 Route::get('ajax/generate-otp-email', 'Client\ClientController@generateOtpEmail')->name('ajax.generate-otp-email');
 Route::post('ajax/verify-otp-email', 'Client\ClientController@verifyOtpEmail')->name('ajax.verify-otp-email');

 Route::get('ajax/generate-otp-phone', 'Client\ClientController@generateOtpPhone')->name('ajax.generate-otp-phone');
 Route::post('ajax/verify-otp-phone', 'Client\ClientController@verifyOtpPhone')->name('ajax.verify-otp-phone');

 Route::post('/admingetutility',[
	'uses' => 'Utility\UtilityController@admingetutility'
 ]);
Route::post('/ajaxgetsalescenters',[
	'uses' => 'Salescenter\SalescenterController@ajaxgetsalescenters'
 ])->name('getSalesCenterByClientId');
Route::post('/ajaxgetlocation',[
	'uses' => 'Salescenter\SalescenterController@ajaxgetlocation'
 ])->name('getSalesCenterLocationOptions');
 Route::post('/ajax/ajaxgetlocationbyclient',[
	'uses' => 'Salescenter\SalescenterController@ajaxgetlocationbyclient'
 ]);
 Route::get('/ajax/get-location-channels',[
	'uses' => 'Salescenter\SalescenterController@getLocationChannels'
 ])->name('getLocationChannels');
 Route::post('/ajaxgetdashboardreport',[
	'uses' => 'Reports\ReportsController@ajaxgetdashboardreport'
 ]);
 Route::post('/ajaxgetclientdashboardreport',[
	'uses' => 'Reports\ReportsController@ajaxgetclientdashboardreport'
 ]);
 Route::post('/ajaxclientUtilities',[
	'uses' => 'Programs\ProgramsController@clientUtilities'
 ]);
 Route::post('ajax/verifyagent/',[
	'uses' => 'Admin\TelesalesVerificationController@verifyAgent'
 ])->name('telesaleverifyagent');

 Route::post('ajax/verifytelesale/',[
	'uses' => 'Admin\TelesalesVerificationController@verifyTelesale'
 ])->name('telesaleverifysaleid');

 Route::post('ajax/getverificationcontent/',[
	'uses' => 'Admin\TelesalesVerificationController@verifyLead'
 ])->name('telesaleverifylead');

Route::get('ajax/identityVerification/',[
    'uses' => 'Admin\TelesalesVerificationController@identityVerification'
])->name('identityverification');

 Route::post('ajax/saveuseranswer/',[
	'uses' => 'Admin\TelesalesVerificationController@saveuseranswer'
 ])->name('saveuseranswer');




 Route::post('ajax/replicate/',[
	'uses' => 'Admin\TelesalesVerificationController@clonelead'
 ])->name('telesale_clone_lead');

 Route::post('ajax/updatelead/',[
	'uses' => 'Admin\TelesalesVerificationController@updateleaddata'
 ])->name('telesale_update_lead');

 Route::post('ajax/cancellead/',[
	'uses' => 'Admin\TelesalesVerificationController@cancellead'
 ])->name('telesale_cancellead');

 Route::post('ajax/getworkflows/',[
	'uses' => 'Client\ClientController@getworkflows'
 ])->name('ajax-client-workflow');

 Route::post('ajax/compliance-mapping/{client_id}',[
	'uses' => 'Client\ComplianceController@mapoptions'
 ])->name('client.compliance-mapoptions');
 Route::post('ajax/validateclient/',[
	'uses' => 'Client\ClientController@validate_id'
 ])->name('ajax-validateclient');

 Route::any('ajax/getleads/',[
	'uses' => 'Salesagent\SalesagentController@getleads'
 ])->name('ajax-getleads');

 Route::post('ajax/schedulecall/',[
	'uses' => 'Salesagent\SalesagentController@schedulecall'
 ])->name('ajax-schedulecall');

 Route::any('ajax/getzipcodeslist/',[
	'uses' => 'Utility\ZipcodeController@getzipcodeslist'
 ])->name('ajax-getzipcodeslist');

 Route::any('ajax/getclientsbystatus/',[
	'uses' => 'Client\ClientController@getclientsbystatus'
 ])->name('ajax-getclientsbystatus');

 Route::post('ajax/getprogramsforreport',[
	'uses' => 'Reports\ReportsController@ajaxgetAllProgramsForReport'
 ]);
 Route::post('ajax/getsalesagentforreport',[
	'uses' => 'Reports\ReportsController@ajaxgetAllsalesagentsForReport'
 ]);

 Route::any('/ajax/logout-inactive-sales-agent',[
	'uses' => 'Activity\ActivityController@clearInactivesalesagents'
]);
Route::any('/ajax/logoutsalesagent',[
	'uses' => 'Activity\ActivityController@logoutsalesagent'
])->name('logoutsalesagent');


Route::post('ajax/firstnameverify/',[
    'uses' => 'Admin\TelesalesVerificationController@firstNameVerify'
])->name('first-name-verify');


 /* Verify users  */

	Route::get('{id}/verify/{verification_code}',[
		'uses' => 'Auth\LoginController@verify'
	], function ($id,$verification_code) {
			if(strlen($id)==3){
				$user = 	App\User::where([
								['client_id', '=', $id],
								['verification_code', '=', $verification_code],
						])->firstOrFail();
			}else if(strlen($id)==4){
				$user = 	App\User::where([
								['salescenter_id', '=', $id],
								['verification_code', '=', $verification_code],
						])->firstOrFail();

			}else{
				return abort(404);
			}

	});
	Route::get('/verify/{verification_code}',[
		'uses' => 'Auth\LoginController@verifytpvuser'
	], function ($verification_code) {
			   		$user = 	App\User::where([
								['verification_code', '=', $verification_code],
						])->firstOrFail();


	});

	Route::post('{id}/verification/{verification_code}',[
		'uses' => 'Auth\LoginController@verification'
	]);


/* user login */

Route::get('{id}/login',[
	'uses' => 'Auth\LoginController@userlogin'
], function ($id) {
		if(strlen($id) != '3'){
		 	return abort(404);
		}

})->name('user.login');





/* For Call assignment */


// Route::any('assignment',[
// 	'uses' => 'Calls\CallsController@assignment'
// ])->name('assignment');

// Route::any('create-task',[
// 	'uses' => 'Calls\CallsController@createtask'
// ])->name('create-task');

// Route::any('accept-reservation',[
// 	'uses' => 'Calls\CallsController@acceptreservation'
// ])->name('accept-reservation');

// Route::any('incoming-call',[
// 	'uses' => 'Calls\CallsController@incomingcall'
// ])->name('incoming-call');

// Route::any('enqueue-call',[
// 	'uses' => 'Calls\CallsController@enqueuecall'
// ])->name('enqueue-call');

Route::any('agent',[
	'uses' => 'Calls\CallsController@agent'
])->name('agent');

Route::any('newcall',[
	'uses' => 'Calls\CallsController@newCall'
])->name('newcall');
Route::any('agent_answer',[
	'uses' => 'Calls\CallsController@agent_answer'
])->name('agent_answer');


Route::any('token',[
	'uses' => 'Token\TokenController@newToken'
])->name('token');


// Route::post('/{agent_id}/token',
//     ['uses' => 'Token\TokenController@token', 'as' => 'agent-token']
// );

Route::any('conference/incoming-call',[
	'uses' => 'Conference\ConferenceController@incomingcall',
	 'as' => 'conference-incoming-call'
]);

Route::any('conference/token',[
	'uses' => 'Conference\ConferenceController@agentToken',
	 'as' => 'conference-token'
]);
Route::any('assignment',[
	'uses' => 'Conference\ConferenceController@assignment'
])->name('assignment');



Route::any('recordingstatus',[
	'uses' => 'Conference\ConferenceController@recordingstatus'
])->name('recordingstatus');

Route::post('conference/connect/client',
    ['uses' => 'Conference\ConferenceController@connectClient', 'as' => 'conference-connect-client']
);
Route::post('conference/wait',
    ['uses' => 'Conference\ConferenceController@wait', 'as' => 'conference-wait']
);
Route::post('conference/connect/{conference_id}/agent1',
    ['uses' => 'Conference\ConferenceController@connectAgent1', 'as' => 'conference-connect-agent1']
);
Route::post('conference/connect/{conference_id}/agent2',
    ['uses' => 'Conference\ConferenceController@connectAgent2', 'as' => 'conference-connect-agent2']
);
Route::post('conference/{agent_id}/call',
    ['uses' => 'Conference\ConferenceController@callAgent2', 'as' => 'conference-call']
);



Route::any('conference/assignment',[
	'uses' => 'Conference\ConferenceController@assignment'
])->name('conference.assignment');

Route::any('conference/assignment_redirect',[
	'uses' => 'Conference\ConferenceController@assignment_redirect'
])->name('conference.assignment_redirect');
Route::any('conference/dailClientNumber',[
	'uses' => 'Conference\ConferenceController@dailClientNumber'
])->name('conference.dailClientNumber');

Route::any('conference/create-task',[
	'uses' => 'Conference\ConferenceController@createtask'
])->name('conference.create-task');

Route::any('conference/accept-reservation',[
	'uses' => 'Conference\ConferenceController@acceptreservation'
])->name('conference.accept-reservation');


Route::any('conference/enqueue-call',[
	'uses' => 'Conference\ConferenceController@enqueuecall'
])->name('conference.enqueue-call');
Route::any('downloadrecordings',[
	'uses' => 'Conference\ConferenceController@downloadrecordings'
])->name('downloadrecordings');

Route::any('getcallduration',[
	'uses' => 'Conference\ConferenceController@GetCallDuration'
])->name('getcallduration');

/* Update activity */
Route::post('activityupdate',[
	'uses' => 'Activity\ActivityController@updateLastActivity'
])->name('activityupdate');
Route::any('clearinactive',[
	'uses' => 'Activity\ActivityController@clearInactiveUsers'
])->name('clearinactive');
Route::any('logout-inactive-tpv-agent',[
	'uses' => 'Activity\ActivityController@checkActive'
])->name('checkactive');

Route::any('logout-tpv-admin',[
    'uses' => 'Activity\ActivityController@logoutAdmin'
])->name('checkadminactive');

Route::get('/batchexportdaily', [
	'uses' => 'Reports\ReportsController@exportfiletoftp'
	]
 )->name('batchexportdaily');

 Route::get('/sparkbatchexportdaily', [
	'uses' => 'Reports\ReportsController@sparkexportfiletoftp'
	]
 )->name('sparkbatchexportdaily');

 /* If sales agent don't have any sale in 30 days then deactivate his account */

 Route::any('/deactivatesalesagentaccount',[
	'uses' => 'User\UserController@deactivatesalesagentaccount'
 ])->name('deactivatesalesagentaccount');


Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function() {
    Route::any('commodity/index', 'Client\CommodityController@index')->name('commodity.index');
    Route::any('commodity/store', 'Client\CommodityController@store')->name('commodity.store')->middleware('permission:add-new-commodity|edit-commodity','isActiveClient');
    Route::get('commodity/edit', 'Client\CommodityController@edit')->name('commodity.edit')->middleware('permission:edit-commodity');
    Route::delete('commodity/{commodity}', 'Client\CommodityController@destroy')->name('commodity.destroy')->middleware('permission:delete-commodity');
	Route::get('/commodity/all/{client_id}','Client\CommodityController@getCommodities')->name('client.getCommodities');
	Route::get('/get-commodity-units','Client\CommodityController@getCommodityUnit')->name('client.getCommodityUnit');
	Route::any('brand-contact/store','Utility\BrandContactsController@store')->name('brand-contact.store')->middleware('permission:add-new-brand-contact|edit-brand-contact','isActiveClient');
	Route::delete('brand-contact/{id}','Utility\BrandContactsController@destroy')->name('brand-contact.destroy')->middleware('permission:delete-brand-contact');
    Route::get('/brand-contact/{client_id}','Utility\BrandContactsController@list')->name('brand-contact.list');

    Route::get('/settings/edit-workspace','SettingsController@editWorkspace')->name('settings.editWorkspace')->middleware('permission:edit-settings');
    Route::post('/settings/save-workspace','SettingsController@saveWorkspace')->name('settings.saveWorkspace')->middleware('permission:edit-settings');

    Route::get('get-all-leads',[
    'middleware' => ['permission:generate-lead-detail-report'],
	'uses' => 'Admin\TelesalesVerificationController@index'
	 ])->name('telesales.getLeads');

	 Route::post('leads/delete',[
		'middleware' => ['permission:delete-lead-detail-report'],
		'uses' => 'Admin\TelesalesVerificationController@deleteLead'
	])->name('telesales.deleteLead');

 	Route::get('leads/show/{id}',[
 	'middleware' => ['permission:generate-lead-detail-report'],
	'uses' => 'Admin\TelesalesVerificationController@show'
 	])->name('telesales.show');

    Route::get('critical-logs/{id}/show',[
    	'middleware' => ['permission:generate-critical-alert-report'],
        'uses' => 'Reports\ReportsController@criticalLogsShow'
    ])->name('critical-logs.show');

    Route::get('critical-logs/{id}/pdf',[
        'uses' => 'Reports\ReportsController@criticalLogsPdf'
    ])->name('critical-logs.export-pdf');

 	Route::get('leads/contract/download/{id}',[
 	'middleware' => ['permission:generate-lead-detail-report'],
	'uses' => 'Admin\TelesalesVerificationController@contractPdfDownload'
 	])->name('telesales.contractPdfDownload');


 	Route::get('self-verification/allowed/zipcode/create',[
	'uses' => 'Admin\SelfVerificationAllowedZipcodeController@create'
 	])->name('selfVerificationAllowedZipcode.create');

 	Route::post('self-verification/allowed/zipcode/store',[
	'uses' => 'Admin\SelfVerificationAllowedZipcodeController@store'
 	])->name('selfVerificationAllowedZipcode.store');

 	Route::resource("customerType", "Client\CustomerTypeController");
 	Route::get('get/customerType/{client_id}',[
	'uses' => 'Client\CustomerTypeController@getCustomerType'
 	])->name('client.getCustomerType');

});

Route::group(['prefix' => 'admin/client/settings', 'middleware' => 'auth'], function() {
	Route::resource("customFieldProgram", "Programs\SettingsController")->only(['index','create','store']);
	Route::post("deleteRestriction", "Programs\SettingsController@deleteTimeZoneRestriction")->name("deleteRestriction");
	Route::post("editRestriction", "Programs\SettingsController@editTimeZoneRestriction")->name("editRestriction");
});

Route::group(['prefix' => 'admin/client/twilio', 'middleware' => 'auth'], function() {

	Route::get('/workspace/{client_id}','Client\TwilioController@getWorkSpaceByClient')->name('twilio.getWorkSpaceByClient');
	Route::post('/workspace/store/{client_id}','Client\TwilioController@saveWorkSpace')->name('twilio.saveWorkSpace');
	Route::post('/workspace/delete/{client_id}','Client\TwilioController@deleteWorkSpace')->name('twilio.deleteWorkSpace');

	Route::get('/workflow/{client_id}','Client\TwilioController@getWorkflowByClient')->name('twilio.getWorkflowByClient');
	Route::post('/workflow/store/{client_id}','Client\TwilioController@saveWorkflow')->name('twilio.saveWorkflow')->middleware(['permission:edit-workflow|add-workflow','isActiveClient']);
	Route::post('/workflow/delete/{client_id}','Client\TwilioController@deleteWorkflow')->name('twilio.deleteWorkflow')->middleware(['permission:delete-workflow','isActiveClient']);

	Route::get('/all-numbers/{client_id}','Client\TwilioController@numbers')->name('twilio.numbers');
	Route::get('/number/{client_id}','Client\TwilioController@getNumber')->name('twilio.getNumber')->middleware(['permission:view-twilio-number']);
	Route::post('/number/store/{client_id}','Client\TwilioController@saveNumber')->name('twilio.saveNumber')->middleware(['permission:edit-twilio-number|add-twilio-number','isActiveClient']);
	Route::post('/number/delete','Client\TwilioController@deleteNumber')->name('twilio.deleteNumber')->middleware(['permission:delete-twilio-number']);
});
Route::group(['prefix' => 'admin/users', 'middleware' => 'auth'], function() {
	Route::get('/all-users/','Admin\UserController@getAllUsers')->name('admin.all.users')->middleware(['permission:all-users']);
	Route::get('/all-agents/','Admin\UserController@getAllAgents')->name('admin.all.agents')->middleware(['permission:view-all-agents']);
	Route::get('/sales-users/','Admin\UserController@getSalesCenterUser')->name('admin.sales.users')->middleware(['permission:view-sales-users']);
	Route::get('/sales-agents/','Admin\UserController@getSalesCenterAgent')->name('admin.sales.agents')->middleware(['permission:view-sales-agents']);
	Route::get('/update-lead/','Admin\UserController@updateLead')->name('update.lead')->middleware(['permission:update-lead-manually']);;
	Route::post('/update-lead/','Admin\UserController@updateLead')->name('update.lead')->middleware(['permission:update-lead-manually']);;
	// Routes for bulk upload option of sales agents
	Route::get('/bulk-upload','Admin\UserController@salesAgentBulkUpload')->name('admin.sales.agents.bulk-upload')->middleware(['permission:view-sales-agents']);
	Route::post('/save-bulk-upload','Admin\UserController@saveSalesAgentBulkUpload')->name('admin.sales.agents.save-bulk-upload')->middleware(['permission:view-sales-agents']);
	Route::get('/download-sales-agents-sample','Admin\UserController@downloadSalesAgentSampleSheet')->name('admin.sales.agents.download-sales-agents-sample')->middleware(['permission:view-sales-agents']);
});
Route::get('/questions', 'Admin\TpvagentController@questions')->name('tpvagents.questions');
Route::get('/twillio-number', 'Client\TwilioController@getTwilioNumber')->name('tpvagents.twillio_number');

//Twilio Assignment & TwiML Routes
Route::group(['prefix' => 'twilio'], function() {
    Route::post('/generate-voice-otp-message/{id}', 'Conference\ConferenceController@generateVoiceOTPMessage')->name('generate-voice-otp-message');
    Route::any('inbound-call-twiml', 'AgentPanel\TPVAgent\TwilioController@inboundCallTwiML')->name('twilio.inbound-call-twiml');
	Route::any('outbound-call-twiml', 'AgentPanel\TPVAgent\TwilioController@outboundCallTwiML')->name('twilio.outbound-call-twiml');
	Route::post('tpv-ivr-twiml/{leadErr?}', 'AgentPanel\TPVAgent\TPVIVRController@index')->name('twilio.tpv-ivr-call-twiml');
	Route::post('tpv-ivr-twiml-gather/{leadId?}/{language?}/{position?}/{lastPos?}/{wCount?}/{emptyCount?}/{isChild?}/{childPos?}/{totalQues?}/{currentQues?}/{lastCurrQues?}/{leadChildCount?}', 'AgentPanel\TPVAgent\TPVIVRController@gatherInput')->name('twilio.tpv-ivr-gather');
	Route::get('tpv-ivr-twiml-verify-lead/{leadId?}/{language?}/{position?}/{lastPos?}/{wCount?}/{emptyCount?}', 'AgentPanel\TPVAgent\TPVIVRController@verifyLead')->name('twilio.tpv-ivr-verify-lead');
	Route::get('tpv-ivr-twiml-wrong-input/{leadId?}/{language?}/{position?}/{lastPos?}/{wCount?}/{emptyCount?}', 'AgentPanel\TPVAgent\TPVIVRController@handleWrongInput')->name('twilio.tpv-ivr-handle-wrong-input');
	Route::get('tpv-ivr-twiml-decline/{leadId?}/{language?}/{position?}/{lastPos?}/{wCount?}/{emptyCount?}{isChild?}/{childPos?}/{totalQues?}/{currentQues?}/{lastCurrQues?}/{leadChildCount?}', 'AgentPanel\TPVAgent\TPVIVRController@declineSale')->name('twilio.tpv-ivr-decline');
	Route::get('tpv-ivr-twiml-zipcode/{leadId?}/{language?}/{position?}/{lastPos?}/{wCount?}/{emptyCount?}/{isZip?}', 'AgentPanel\TPVAgent\TPVIVRController@askZipcode')->name('twilio.tpv-ivr-zipcode');	
});

Route::group(['prefix' => 'callbacks'], function() {
		//Twilio callback routes
    Route::group(['prefix' => 'twilio'], function() {
			Route::post('/outbound-voice-recording', 'AgentPanel\TPVAgent\TwilioController@outboundVoiceRecording')->name('twilio.callbacks.outbound-voice-recording');
			Route::post('/outbound-voice-recording-callback', 'AgentPanel\TPVAgent\TwilioController@outboundRecordingCallback')->name('twilio.outbound-voice-recording.callback');
			Route::post('ivr-recording-callback', 'AgentPanel\TPVAgent\TPVIVRController@RecordingsCallbackIVR')->name('twilio.ivr-recording-callback');
        Route::post('/workflow-assignment', 'AgentPanel\TPVAgent\TwilioController@workflowAssignment')->name('twilio.callbacks.workflow-assignment');
		Route::post('/workspace-assignment', 'AgentPanel\TPVAgent\TwilioController@workspaceAssignment')->name('twilio.callbacks.workspace-assignment');
		Route::post('/ivr-tpv', 'AgentPanel\TPVAgent\TPVIVRController@ivrTpvAssignment')->name('twilio.callbacks.ivr-assignment');
	});

});
Route::post('callbacks/customerio/event-executed',[
	'uses' => 'Client\ClientController@storeCriticalLog'
 	])->name('store.criticalLog');

//signature upload routes
Route::group(['prefix' => 'sign'], function() {
	Route::get('c/{tmp_lead_id}', 'Signature\SignatureController@create')->name('signature.create');
	Route::get('signature-success', 'Signature\SignatureController@successOrError')->name('signature.success');
	Route::post('store/{tmp_lead_id}', 'Signature\SignatureController@store')->name('signature.store');
});

// route::get("/googlemap",'DemoMapController@map')->name('google.map');
route::get("/demoTwilio",'DemoMapController@twilioStatistics')->name('demo.twilio');
//This route is used for devloper purpose only
Route::get('test-route/{id}', 'Admin\DashboardController@testDashboard')->middleware('auth');


// Route for the Dashboard Agent Report task.
Route::get('admin/dashboard/agent-report', 'Admin\DashboardController@agentReport')->name('reports.agent-report-data')->middleware('auth');
Route::get('admin/dashboard/agent-activity-duration-report', 'Admin\DashboardController@agentActivityDurationReport')->name('reports.agent-activity-duration-report')->middleware('auth');

