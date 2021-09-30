<?php

namespace App\Services;

use App\models\ClientTwilioNumbers;
use App\models\FormField;
use App\models\TelesalesSelfVerifyExpTime;
use Segment;
use App\User;
use App\models\Client;

class SegmentService {

    public function __construct() {

    }

    /**
     * This method is used for create indentity of particular lead
     * @param $lead
     */
    public function createIdentity($lead) {
        // for check hunt group settings is on or off
        if (!isOnSettings(array_get($lead,'client_id'),'is_enable_hunt_group')) {
            \Log::error("Notify 360 settings is off for client id: " . array_get($lead, 'client_id'));
            return false;
        }
        $toData = $this->toLeadIdentity($lead);

        if ($toData['traits']['email']) {
            $this->sendIdentityToSegment($toData);
            \Log::info("Segment identity created for lead: " . array_get($lead, 'id'));
            return true;
        }
        else {
            \Log::error("No email available available for created lead with id: " . array_get($lead, 'id'));
            return false;
        }
    }

    /**
     * For check details of lead identity
     * @param $lead
     */
    public function toLeadIdentity($lead) {
        $leadIdentity = [];
        $leadIdentity['userId'] = config('segment.segment_identifier_prefix') . "-" . $lead->refrence_id;
        $leadIdentity['traits'] = [];
        $leadIdentity['traits']['name'] = "";
//        $leadIdentity['traits']['leadReferenceId'] = array_get($lead, 'refrence_id', NULL);

        //Retrieve Primary email and Name fields of lead
        $primaryNameField = FormField::where('form_id', array_get($lead, 'form_id'))->where('type', 'fullname')->where('is_primary', true)->first();
        $primaryEmailField = FormField::where('form_id', array_get($lead, 'form_id'))->where('type', 'email')->where('is_primary', true)->first();
        $primaryPhoneNumField = FormField::where('form_id', array_get($lead, 'form_id'))->where('type', 'phone_number')->where('is_primary', true)->first();

        //Retrieve data if primary name field available for form
        $primaryNameData = $lead->telesalesData()->where('field_id', array_get($primaryNameField, 'id'))->where('meta_key', 'first_name')->where('telesale_id', $lead->id)->first();
        $primaryLastData = $lead->telesalesData()->where('field_id', array_get($primaryNameField, 'id'))->where('meta_key', 'last_name')->where('telesale_id', $lead->id)->first();

        //Retrieve primary email field data if provided for lead
        $primaryEmailData = $lead->telesalesData()->where('field_id', array_get($primaryEmailField, 'id'))->where('meta_key', 'value')->where('telesale_id', $lead->id)->first();

        //Retrieve primary email field data if provided for lead
        $primaryPhoneNumberData = $lead->telesalesData()->where('field_id', array_get($primaryPhoneNumField, 'id'))->where('meta_key', 'value')->where('telesale_id', $lead->id)->first();

        $leadIdentity['traits']['name'] = implode(" ", [array_get($primaryNameData, 'meta_value'), array_get($primaryLastData, 'meta_value')]);
        $leadIdentity['traits']['email'] = array_get($primaryEmailData, 'meta_value', NULL);
        $leadIdentity['traits']['created_at'] = date('Y-m-d H:i:s', strtotime(array_get($lead, 'created_at')));
        $leadIdentity['traits']['lead_phone_number'] = array_get($primaryPhoneNumberData, 'meta_value', NULL);
        
        //Retrieving Customer call in numbers
        $customerCallInVerificationNumber = ClientTwilioNumbers::where('client_id', array_get($lead, 'client_id'))->where('type', 'customer_call_in_verification')->first();

       //Initially sending blank self verification link, when self verification link generates then update its track and send updated track to
        $leadIdentity['traits']['self_verification_link'] = "";

        $leadIdentity['traits']['customer_phonenumber'] = array_get($customerCallInVerificationNumber, 'phonenumber');

        $leadIdentity['traits']['server'] = strtolower(config('segment.segment_identifier_prefix'));
        $leadIdentity['traits']['type'] = "lead";

        $clientName = Client::where('id',array_get($lead, 'client_id'))->select('name')->first();
        $leadIdentity['traits']['client_id'] = array_get($lead, 'client_id');
        $leadIdentity['traits']['client_name'] = $clientName->name;

        $leadIdentity['traits']['lead_type'] = $lead->type;
        $leadIdentity['traits']['is_enable_self_tpv_tele'] = isOnSettings($lead->client_id, 'is_enable_self_tpv_tele') ? 1 : 0 ;
        $leadIdentity['traits']['is_enable_self_tpv_d2d'] = isOnSettings($lead->client_id, 'is_enable_self_tpv_d2d') ? 1 : 0 ;
        return $leadIdentity;
    }

    /**
     * For send identity to segment
     * @param $toData
     */
    public function sendIdentityToSegment($toData) {
        return Segment::identify($toData);
        return $segment;
    }

    /**
     * For create track of particular lead
     * @param $lead
     */
    public function createTrack($lead) {
        // for check hunt group settings is on or off
        if (isOnSettings(array_get($lead,'client_id'),'is_enable_hunt_group')) {
            $toData = $this->toLeadCreationTrack($lead);
            return $this->sendTrackToSegment($toData);
        } else {
            \Log::error("Notify 360 settings is off for client id: " . array_get($lead, 'client_id'));
            return false;
        }
    }

    /**
     * For lead creation track
     * @param $lead
     */
    public function toLeadCreationTrack($lead) {
        $leadCreationTrack = [];
        $leadCreationTrack['userId'] = config('segment.segment_identifier_prefix') . "-" . array_get($lead, 'refrence_id');
        $leadCreationTrack['event'] = config('constants.SEGMENT_TRACK_LEAD_CREATE_TEXT');
        $leadCreationTrack['lead_status'] = array_get($lead, 'id');
        $leadCreationTrack['lead_reference_id'] = array_get($lead, 'refrence_id');
        return $leadCreationTrack;
    }

    /**
     * For send track to segment
     * @param $toData
     */
    public function sendTrackToSegment($toData) {
        return Segment::track($toData);
    }

    /**
     * For create track for lead status update
     * @param $lead, $oldStatus, $updatedStatus
     */
    public function createLeadStatusUpdatedTrack($lead, $oldStatus, $updatedStatus){
        // for check hunt group settings is on or off
        if (isOnSettings(array_get($lead,'client_id'),'is_enable_hunt_group')) {
            $toData = $this->toLeadStatusUpdatedTrack($lead, $oldStatus, $updatedStatus);
            return $this->sendTrackToSegment($toData);
        } else {
            \Log::error("Notify 360 settings is off for client id: " . array_get($lead, 'client_id'));
            return false;
        }
    }

    /**
     * For update status track of lead
     * @param $lead, $oldStatus, $updatedStatus
     */
    public function toLeadStatusUpdatedTrack($lead, $oldStatus, $updatedStatus) {
        $leadFollowupTrack = [];
        $leadFollowupTrack['userId'] = config('segment.segment_identifier_prefix') . "-" . array_get($lead, 'refrence_id');
        $leadFollowupTrack['event'] = config('constants.SEGMENT_TRACK_LEAD_STATUS_UPDATE_TEXT');
        $leadFollowupTrack['properties'] = [];
        $leadFollowupTrack['lead_reference_id'] = array_get($lead, 'refrence_id');
        $leadFollowupTrack['properties']['old_status'] = $oldStatus;
        $leadFollowupTrack['properties']['new_updated_status'] = $updatedStatus;
        return $leadFollowupTrack;
    }

    /**
     * This method is used for get self verification link
     * @param $lead
     */
    public function getSelfVerificationLink($lead) {
        $emailLink = TelesalesSelfVerifyExpTime::where('telesale_id', array_get($lead, 'id'))->where('verification_mode', config()->get('constants.SELF_VERIFICATION_LINK_EMAIL_MODE'))->first();
        if (empty($emailLink)) {
            return "";
        } else {
            return route('sendverificationlink', [base64_encode(array_get($lead, 'id')),'email']);
        }
    }

    /**
     * Update lead track after generating a lead
     * @param $lead
     */
    public function updateLeadTrackForEmailLink($lead) {
        // for check hunt group settings is on or off
        if (isOnSettings(array_get($lead,'client_id'),'is_enable_hunt_group')) {
            $toTrack = $this->toVerificationLinkUpdatedTrack($lead);
            return $this->sendTrackToSegment($toTrack);
        } else {
            \Log::error("Notify 360 settings is off for client id: " . array_get($lead, 'client_id'));
            return false;
        }
    }

    /**
     * Prepare an array to send for a track while generating a link
     * @param $lead
     */
    public function toVerificationLinkUpdatedTrack($lead)
    {
        $leadTrack = [];
        $leadTrack['userId'] = config('segment.segment_identifier_prefix') . "-" . array_get($lead, 'refrence_id');
        $leadTrack['event'] = config('constants.SEGMENT_TRACK_EMAIL_LINK_UPDATE_TEXT');
        $leadTrack['properties'] = [];
        $leadTrack['lead_reference_id'] = array_get($lead, 'refrence_id');
        $leadTrack['properties']['self_verification_link'] = $this->getSelfVerificationLink($lead);
        return $leadTrack;
    }
}
