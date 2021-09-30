<?php

namespace App\Traits;

use App\models\ClientWorkspace;
use Log;
use App\models\ClientTwilioNumbers;

trait TwilioTrait {

    /**
     * For Retrieve first workspace from table, as we are using same workspace for each client within system
     */
    public function getWorkspaceDetails() {
        return ClientWorkspace::select('id', 'workspace_id')->first();
    }

    /**
     * This method is used to Retrieve client number
     */
    public function getClientNumberDetails($client, $type = 'customer_call_in_verification') {
        return ClientTwilioNumbers::select('id', 'phonenumber', 'client_workflowid', 'type')->first();
    }
}
