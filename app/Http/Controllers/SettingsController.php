<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\ClientWorkspace;
use App\models\Client;
use App\models\ClientWorkflow;

class SettingsController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     * @return \Illuminate\Http\Response
     */
    public function editWorkspace(Request $request)
    {
        $workspace=ClientWorkspace::first();

        return view('settings.workspace.edit',compact('workspace'));
    }

     /**
     * Display the specified resource.
     * @return \Illuminate\Http\Response
     */
    public function saveWorkspace(Request $request)
    {
    	$request->validate([
    		'workspace_id'=>'required',
    		'workspace_name'=>'required'
        ]);

    	$clients = Client::select('id')->get();

    	if (count($clients) <= 0 ) {
            return redirect()->back()->with('error', 'No active clients found !!');
        }

        ClientWorkspace::truncate();
    	foreach ($clients as $client) {
            ClientWorkspace::create([
                'client_id' => $client->id,
                'workspace_id' => $request->get('workspace_id'),
                'workspace_name' => $request->get('workspace_name')
            ]);
        }

        \DB::table('client_twilio_workflowids')->where('id', '>', 0)->update([
            'workspace_id' => $request->get('workspace_id')
        ]);

    	return redirect()->back()->with('success','Workspace successfully updated.');
    }
}
