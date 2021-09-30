<?php


$constants = [
    /* Sales agent lead detail fields */
    'FormFields' => array(
        'Program',
        'Account Number',
        'Authorized First name',
        'Authorized Middle initial',
        'Authorized Last name',
        'rate',
        'etf',
        // 'Program Code',
        'msf',
        'term',
        'Account Number Length',
        'Account Number Type',
        'accountnumbertypename',
        'GasServiceZip',
        'GasServiceCity',
        'ServiceState',
        'GasServiceAddress',
        'GasServiceAddress2',
        'Gas Billing first name',
        'Gas Billing middle name',
        'Gas Billing last name',
        'GasBillingAddress',
        'GasBillingAddress2',
        'GasBillingZip',
        'GasBillingCity',
        'GasBillingState',
        'gasutility',
        'gas_rate',
        'gas_term',
        'gas_msf',
        'gasutility',
        'GasProgram',
        'Gas Account Number',
        'ServiceZip',
        'ServiceCity',
        'ServiceAddress',
        'ServiceAddress2',
        'Electric Billing first name',
        'Electric Billing middle name',
        'Electric Billing last name',
        'ElectricBillingAddress',
        'ElectricBillingAddress2',
        'ElectricBillingZip',
        'ElectricBillingCity',
        'ElectricBillingState',
        'electricutility',
        'ElectricProgram',
        'electric_rate',
        'electric_term',
        'electric_msf',
        'electric_etf',
        'Electric Account Number',
        'Agent Name Key',
        'Service Reference Id',
        'Gas Agent Name Key',
        'Gas Service Reference Id',
        'Electric Agent Name Key',
        'Electric Service Reference Id'

    ),
    "Common_fields_for_script" => array(
        "[Tpvagent]",
        "[Client]",
        "[ClientPhone]",
        "[Client ID Verification Box]",
        "[Agent ID Verification Box]",
        "[Telesale ID Verification Box]",
        "[Lead Verification ID]",
        "[Date]",
        "[Time]"
    ),
    "GasOrElectric" => array(
        "[Program Code]",
        "[UDCAccountCode]",
        "[accountnumbertypename]",
        "[ElectricUDCAccountCode]",
        "[GasUDCAccountCode]",
        "[rate]",
        "[term]",
        "[msf]",
        "[etf]",
        "[Utility]",
        "[Program]",
        "[Account Number]",
        "[Authorized First name]",
        "[Authorized Middle initial]",
        "[Authorized Last name]",

    ),
    "DualFuel" => array(
        "[Authorized First name]",
        "[Authorized Middle initial]",
        "[Authorized Last name]",
        "[Gas Billing first name]",
        "[Gas Billing middle name]",
        "[Gas Billing last name]",
        "[Electric Billing first name]",
        "[Electric Billing middle name]",
        "[Electric Billing last name]",
        "[Gas Account Number]",
        "[gasutility]",
        "[gas_MarketCode]",
        "[gas_rate]",
        "[gas_term]",
        "[gas_msf]",
        "[gas_etf]",
        "[GasProgram]",
        "[electricutility]",
        "[ElectricProgram]",
        "[electric_MarketCode]",
        "[electric_rate]",
        "[electric_msf]",
        "[electric_term]",
        "[electric_etf]",
        "[Electric Account Number]",
        "[ElectricUDCAccountCode]",
        "[GasUDCAccountCode]",
    ),

    'newFormFields' => array(
      'address' => 'Address',
      'checkbox' => 'Checkbox',
      'email' => 'Email',
      'full_name' => 'Full Name',
      'heading' => 'Heading',
      'label' => 'Label',
      'phone_number' => 'Phone Number',
      'radio' => 'Radio',
      'selectbox' => 'Selectbox',
      'separator' => 'Separator',
      'service_and_billing_address' => 'Service and billing address',
      'textarea' => 'Text area',
      'textbox' => 'Text box'
    ),
    'TAGS_CATEGORY' => [
        'BRAND' ,
        'BRAND CONTACT' ,
        'RATE' ,
        'RATE IN CENT' ,
        'RATE IN TEXT',
        'TERM' ,
        'MSF' ,
        'MSF IN TEXT' ,
        'ETF' ,
        'ETF IN TEXT' ,
        'UTILITY' ,
        'UTILITY ABBREVIATION',
        'PROGRAM' ,
        'PLAN NAME' ,
        'PROGRAM CODE' ,
        'UNIT' ,
        'CUSTOMER TYPE',
        'ACCOUNT NUMBER TYPE',
    ],

    'ADDRESS_TAGS' => [
        'UNITNUMBER' ,
        'ADDRESSLINE1' ,
        'ADDRESSLINE2' ,
        'ZIPCODE' ,
        'CITY' ,
        'COUNTY',
        'STATE' ,
        'COUNTRY' ,
        'LATITUDE' ,
        'LONGITUDE' ,
        'UNITNUMBER' ,
        'SERVICE ADDRESS',
        'SERVICEUNITNUMBER' ,
        'SERVICEADDRESSLINE1' ,
        'SERVICEADDRESSLINE2' ,
        'SERVICEZIPCODE' ,
        'SERVICECITY' ,
        'SERVICECOUNTY' ,
        'SERVICESTATE' ,
        'SERVICECOUNTRY' ,
        'SERVICELATITUDE' ,
        'SERVICELONGITUDE' ,
        'BILLING ADDRESS',
        'BILLINGUNITNUMBER' ,
        'BILLINGADDRESSLINE1' ,
        'BILLINGADDRESSLINE2' ,
        'BILLINGZIPCODE' ,
        'BILLINGCITY' ,
        'BILLINGCOUNTY' ,
        'BILLINGSTATE' ,
        'BILLINGCOUNTRY' ,
        'BILLINGLATITUDE' ,
        'BILLINGLONGITUDE'

    ],

    'GENERAL_TAGS' => [
        'DATE','TIME','TPVAGENT','VERIFICATION CODE' ,'LEAD ID', 'CHANNEL', 'SALES CENTER', 'SALES CENTER LOCATION'
    ],

    'Tags_Category_Names' => [
        1 => 'General Info',
        2 => 'Addresses',
        3 => 'Program Info',
        4 => 'Enrollment Detail'
    ],

    'EST_TIMEZONE' => 'EST',
    'GOOGLE_MAP_API_KEY' => 'AIzaSyC49bXfihl4zZqjG2-iRLUmcWO_PVcDehM',
    'PHONE_NUMBER_TYPE' => 'phone_number',
    'IS_ENABLE_CRITICAL_ALERT_MAIL' => true, // in boolean
    'INACTIVE_TPV_AGENT_TIMEOUT_MINS' => 60,
    'SEGMENT_TRACK_LEAD_CREATE_TEXT' => "Lead created",
    'SEGMENT_TRACK_LEAD_FOLLOWUP_TEXT' => "Followup Created",
    'SEGMENT_TRACK_LEAD_STATUS_UPDATE_TEXT' => 'Lead Status Updated',
    'SEGMENT_TRACK_EMAIL_LINK_UPDATE_TEXT' => 'Email link generated for lead',
    'self_verification_link_expire_time' => 72,   // In Hours
    'salesagentintro','leadcreation','customer_verification','agent_not_found','closing','lead_not_found','after_lead_decline',

    'scripts' => [
        'salesagentintro' => 'Sales agent verification',
        // 'leadcreation' => 'Customer Verification',
        'customer_verification' => 'Live TPV - Telesales Warm Transfer',
        'agent_not_found' => 'Agent not found',
        'closing' => 'Closing',
        'lead_not_found' => 'Lead not found',
        'can_not_transfer' => 'Can’t transfer',
        'after_lead_decline' => 'Decline',
        'customer_call_in_verification' => 'Live TPV - Customer Call In',
        'self_verification' => 'Self Verification',
        'identity_verification' => 'Identity Verification',
        'ivr_tpv_verification' => 'IVR TPV Verification'
    ],
    'SALES_AGENT_ACTIVITY_TYPE' => [
        'clock_in',
        'clock_out',
        'break_in',
        'break_out',
        'arrival_in',
        'arrival_out' // for departure
    ],
    'SALES_AGENT_ACTIVITY_HOURS' => 8,
    'SALES_AGENT_LAST_ACTIVITY_HOURS' => 1,

    'scripts-new-name' => [
        'self_verification' => 'Self TPV lead verification',
        'self_outbound_verification' => 'Self TPV outbound verification - welcome call',
        'customer_verification' => 'Live TPV lead verification',
        'ivr_tpv_verification' => 'IVR TPV Verification',
        'customer_call_in_verification' => 'Customer identity verification - live tpv',
        'salesagentintro' => 'Sales agent identity verification',
        'identity_verification' => 'Customer identity verification - self tpv',
        'agent_not_found' => 'Agent not found',
        'lead_not_found' => 'Lead not found',
        'can_not_transfer' => 'Can’t transfer',
        'closing' => 'Closing (good sale)',
        'after_lead_decline' => 'Declined (bad sale)'

    ],
    "script_type" =>[
        'salesagentintro' => 'Identity verification',
        'customer_verification' => 'Lead verification',
        'self_verification' => 'Lead verification',
        'self_outbound_verification' => 'Lead verification',
        'agent_not_found' => 'Pre lead verification',
        'closing' => 'Post lead verification',
        'lead_not_found' => 'Pre lead verification',
        'can_not_transfer' => 'Pre lead verification',
        'after_lead_decline' => 'Post lead verification',
        'customer_call_in_verification' => 'Identity verification',
        'identity_verification' => 'Identity verification',
        'ivr_tpv_verification' => 'IVR TPV Verification'
    ],
    "general_scripts" => [
        'agent_not_found',
        'lead_not_found',
        'can_not_transfer',
        'closing',
        'after_lead_decline'
    ],
    "script_upload_id" =>[
        'bulk_upload' => '1',
        'single_script' => '2',
    ],
    'script_languages' => [
        'en' => 'English',
        'es' => 'Spanish'
    ],

    'SCRIPT_QUESTION_CONDITION_OPERATOR' => [
        'is_equal_to' => 'Is Equal To',
        'is_not_equal_to' => 'Is Not Equal To',
        'is_greater_than' => 'Is Greater Than',
        'is_less_than' => 'Is Less Than',
        'exists'  => 'Exists',
        'string_contains' => 'String Contains',
        'string_does_not_contains' => 'String Doesnot Contains',
        'matches_regex' => 'Matches Regex'

    ],

    'SCRIPT_QUESTION_CONDITION_OPERATOR_REQUIRE_COMPARISON_VALUE' => [
        'is_equal_to',
        'is_not_equal_to',
        'is_greater_than',
        'is_less_than',
        'string_contains',
        'string_does_not_contains',
        'matches_regex'
    ],

    'SCRIPT_QUESTION_CONDITION_OPERATOR_NOT_REQUIRE_COMPARISON_VALUE' => [
        'exists'
    ],

    'alert_level' => [
        'client' => 'Client',
        'salescenter' => 'Sales Center',
        'sclocation' => 'Sales Center Location'
    ],

    'edit_alert_level' => [
        ['key' => 'client', 'name' => 'Client'],
        ['key' => 'salescenter', 'name' => 'Sales Center'],
        ['key' => 'sclocation', 'name' => 'Sales Center Location'],
    ],

    'alert_for' => [
        'disposition' => 'Disposition',
        'fraudalert' => 'Fraud alert',
    ],

    'ADDRESS_TYPE' => [
        'SERVICE' => 'service',
        'BILLING' => 'billing'
    ],

    'CALL_DURATION' => 5, //In minutes
    'TWILIO_CALL_TYPE_OUTBOUND' => 'outbound',
    'TWILIO_CALL_TYPE_SELFVERIFIED_CALLBACK' => 'self_outbound_verification',

    'TWILIO_PHONE_NUMBER_TYPE' => [
      'CUSTOMER_INBOUND_NUMBER' => 'customer_call_in_verification',
      'AGENT_INBOUND_NUMBER' => 'customer_verification',
    ],
    'TWILIO_IVR_TYPE' =>[
        'ivr_tpv_verification' => 'IVR TPV'
    ],

    'IVR_TPV_VERIFICATION_KEY' => 'ivr_tpv_verification',

    'CALL_DISPLAY_COLUMN_NAME' => [
        'dial_status' => 'Call Attempt Status',
        'schedule_status' => 'Call Status'
    ],
    'AGENT_DEFAULT_NUMBER' => env('AGENT_DEFAULT_NUMBER','855-747-4931'),
    'permissions' => [
            'add-client-user' => 'add-client-user',
            'add-dispositions' => 'add-dispositions',
            'add-new-brand-contact' => 'add-new-brand-contact',
            'add-new-commodity' => 'add-new-commodity',
            'add-new-form' => 'add-new-form',
            'add-program' => 'add-program',
            'add-sales-agents' => 'add-sales-agents',
            'add-sales-center' => 'add-sales-center',
            'add-sales-users' => 'add-sales-users',
            'add-tpv-agents' => 'add-tpv-agents',
            'add-tpv-users' => 'add-tpv-users',
            'add-customer-type' => 'add-customer-type',
            'add-utility-provider' => 'add-utility-provider',
            'add-client' => 'add-client',
            'all-clients' => 'all-clients',
            'all-users' => 'all-users',
            'bulk-upload-utility' => 'bulk-upload-utility',
            'bulk-upload-program' => 'bulk-upload-program',
            'dashboard' => 'dashboard',
            'deactivate-client-user' => 'deactivate-client-user',
            'deactivate-form' => 'deactivate-form',
            'deactivate-program' => 'deactivate-program',
            'deactivate-sales-center' => 'deactivate-sales-center',
            'delete-brand-contact' => 'delete-brand-contact',
            'delete-client-user' => 'delete-client-user',
            'delete-commodity' => 'delete-commodity',
            'delete-dispositions' => 'delete-dispositions',
            'delete-sales-users' => 'delete-sales-users',
            'delete-tpv-agents' => 'delete-tpv-agents',
            'delete-tpv-users' => 'delete-tpv-users',
            'delete-utility' => 'delete-utility',
            'delete-customer-type' => 'delete-customer-type',
            'edit-brand-contact' => 'edit-brand-contact',
            'edit-client-info' => 'edit-client-info',
            'edit-client-user' => 'edit-client-user',
            'edit-client-users' => 'edit-client-users',
            'edit-commodity' => 'edit-commodity',
            'edit-dispositions' => 'edit-dispositions',
            'edit-form' => 'edit-form',
            'edit-sales-agents' => 'edit-sales-agents',
            'edit-sales-center' => 'edit-sales-center',
            'edit-sales-users' => 'edit-sales-users',
            'edit-tpv-agents' => 'edit-tpv-agents',
            'edit-tpv-users' => 'edit-tpv-users',
            'edit-utility' => 'edit-utility',
            'edit-settings' => 'edit-settings',
            'edit-customer-type' => 'edit-customer-type',
            'export-utility' => 'export-utility',
            'export-program' => 'export-program',
            'view-all-agents' => 'view-all-agents',
            'view-client-info' => 'view-client-info',
            'view-client-user' => 'view-client-user',
            'view-client-users' => 'view-client-users',
            'view-forms' => 'view-forms',
            'view-programs' => 'view-programs',
            'generate-recordings-report' => 'generate-recordings-report',
            'view-sales-agents' => 'view-sales-agents',
            'view-sales-center' => 'view-sales-center',
            'view-sales-users' => 'view-sales-users',
            'view-scripts' => 'view-scripts',
            'view-tpv-agents' => 'view-tpv-agents',
            'view-tpv-users' => 'view-tpv-users',
            'view-user-roles' => 'view-user-roles',
            'view-utility' => 'view-utility',
            'view-customer-type' => 'view-customer-type',
            'copy-form' => 'copy-form',
            'deactivate-client' => 'deactivate-client',
            'deactivate-global-admin' => 'deactivate-global-admin',
            'deactivate-client-admin' => 'deactivate-client-admin',
            'deactivate-tpv-admin' => 'deactivate-tpv-admin',
            'deactivate-tpv-qa' => 'deactivate-tpv-qa',
            'deactivate-sc-admin' => 'deactivate-sc-admin',
            'deactivate-sc-location-admin' => 'deactivate-sc-location-admin',
            'deactivate-sc-qa' => 'deactivate-sc-qa',
            'deactivate-sales-agent' => 'deactivate-sales-agent',
            'deactivate-tpv-agent' => 'deactivate-tpv-agent',
            'support' => 'support',
            'view-dispositions' => 'view-dispositions',
            'view-commodities' => 'view-commodities',
            'view-brand-contcts' => 'view-brand-contcts',
            'view-workflow' => 'view-workflow' ,
            'edit-workflow' => 'edit-workflow' ,
            'delete-workflow' => 'delete-workflow' ,
            'add-workflow' => 'add-workflow' ,
            'view-twilio-number' => 'view-twilio-number' ,
            'edit-twilio-number' => 'edit-twilio-number' ,
            'delete-twilio-number' => 'delete-twilio-number' ,
            'add-twilio-number' => 'add-twilio-number' ,
            'generate-lead-detail-report' => 'generate-lead-detail-report',
            'export-lead-detail-report' => 'export-lead-detail-report',
            'filter-lead-detail-report' => 'filter-lead-detail-report',
            'generate-enrollment-report' => 'generate-enrollment-report',
            'export-enrollment-report' => 'export-enrollment-report',
            'filter-enrollment-report' => 'filter-enrollment-report',
            'generate-sales-activity-report' => 'generate-sales-activity-report',
            'export-sales-activity-report' => 'export-sales-activity-report',
            'filter-sales-activity-report' => 'filter-sales-activity-report',
            'generate-critical-alert-report' => 'generate-critical-alert-report',
            'export-critical-alert-report' => 'export-critical-alert-report',
            'filter-critical-alert-report' => 'filter-critical-alert-report',
            'filter-recordings-report' => 'filter-recordings-report',
            'generate-sales-agent-trail' => 'generate-sales-agent-trail',
            'filter-sales-agent-trail' => 'filter-sales-agent-trail',
            'generate-billing-report' => 'generate-billing-report',
            'edit-permission-roles' => 'edit-permission-roles',
            'bulk-upload-dispositions' => 'bulk-upload-dispositions',
            'export-dispositions' => 'export-dispositions',
            'upload-scripts' => 'upload-scripts',
            "delete-client" => "delete-client",
            "delete-sales-center" => "delete-sales-center",
            "delete-sales-center-location" => "delete-sales-center-location",
            "delete-program" => "delete-program",
            "delete-form" => "delete-form",
            "delete-client-user" => "delete-client-user",
            "delete-lead-detail-report" => "delete-lead-detail-report",
            "delete-tpv-admin" => "delete-tpv-admin",
            "delete-tpv-qa" => "delete-tpv-qa",
            "delete-sc-admin" => "delete-sc-admin",
            "delete-sc-qa" => "delete-sc-qa",
            "delete-sc-location-admin" => "delete-sc-location-admin",
            "delete-sales-agent" => "delete-sales-agent",
            "delete-tpv-agent" => "delete-tpv-agent",
            'bulk-upload-sales-users' => 'bulk-upload-sales-users',
            'export-sales-users' => 'export-sales-users',
            'view-client-settings' => 'view-client-settings',
            'edit-client-settings' => 'edit-client-settings',
            'view-alerts' => 'view-alerts',
            'edit-alerts' => 'edit-alerts',
            'edit-program' => 'edit-program',
            'agent-dashboard' => 'agent-dashboard',
            'generate-call-detail-report' => 'generate-call-detail-report',
            'update-lead-manually' => 'update-lead-manually',
            'view-do-not-enroll' => 'view-do-not-enroll',
            'add-do-not-enroll' => 'add-do-not-enroll',
            'delete-do-not-enroll' => 'delete-do-not-enroll',
            'bulk-upload-do-not-enroll' => 'bulk-upload-do-not-enroll',
            'export-do-not-enroll' => 'export-do-not-enroll',
            'view-brand-info' => 'view-brand-info',
            'edit-brand-info' => 'edit-brand-info'
        ],

        'VERIFICATION_METHOD' => [
            'CUSTOMER_INBOUND' => '1',
            'AGENT_INBOUND' => '2',
            'EMAIL' => '3',
            'TEXT' => '4',
            'TPV_Now_Outbound' => '6',
            'IVR_INBOUND' => '5',
        ],

        'VERIFICATION_METHOD_FOR_REPORT' => [
            'Customer Inbound' => '1',
            'Agent Inbound' => '2',
            'Email' => '3',
            'Text' => '4',
            'IVR Inbound' => '5',
            'TPV Now Outbound' => '6'
        ],

        'VERIFICATION_METHOD_FOR_DISPLAY' => [
            '1' => 'Customer Inbound',
            '2' => 'Agent Inbound',
            '3' => 'Email',
            '4' => 'Text',
            '5' => 'IVR Inbound',
            '6' => 'TPV Now Outbound'
        ],

        'VERIFICATION_STATUS_CHART' => [
            'Pending' => 'Pending',
            'Self-verified' => 'Self verified',
            'Verified' => 'Verified',
            'Decline' => 'Declined',
            'Hangup' => 'Disconnected',
            'Cancel' => 'Cancelled',
            'Expired' => 'Expired',
        ],

        'VERIFICATION_STATUS_CHART_LEADS' => [
            'Pending' => 'Pending',
            'Verified' => 'Verified',
            'Declined' => 'Decline',
            'Disconnected' => 'Hangup',
            'Cancelled' => 'Cancel',
        ],

        'SALES_AGENT_TYPE' => [
            'D2D' => 'd2d',
            'TELE' => 'tele'
        ],

        'USER_TYPE_CRITICAL_LOGS' => [
            '1' => 'Customer',
            '2' => 'System'
        ],
        'LEAD_STATUS_CRITICAL_LOGS' => [
            'Pending' => 'Pending',
            'Verified' => 'Verified',
            'Declined' => 'Declined',
            'Cancelled' => 'Cancelled',
            'Expired' => 'Expired',
            'Disconnected' => 'Disconnected',
            'Partial' => 'Partial',
            'self-verified' => 'Self Verified'
        ],
        'OBJECT_TYPE_OF_EVENT_TYPE_16' => 'email', // this value of webhook request
        'CAMPAIGN_ID_OF_EVENT_TYPE_16' => 8, // this value of webhook request
        'ERROR_TYPE_CRITICAL_LOGS' => [
            'Non-critical' => 0,
            'Critical' => 1
        ],
        'EVENT_TYPE_CRITICAL_LOGS' => [
            'Event_Type_1' => 1,
            'Event_Type_2' => 2,
            'Event_Type_3' => 3,
            'Event_Type_4' => 4,
            'Event_Type_5' => 5,
            'Event_Type_6' => 6,
            'Event_Type_7' => 7,
            'Event_Type_8' => 8,
            'Event_Type_9' => 9,
            'Event_Type_10' => 10,
            'Event_Type_11' => 11,
            'Event_Type_12' => 12,
            'Event_Type_13' => 13,
            'Event_Type_14' => 14,
            'Event_Type_15' => 15,
            'Event_Type_16' => 16,
            'Event_Type_17' => 17,
            'Event_Type_18' => 18,
            'Event_Type_19' => 19,
            'Event_Type_20' => 20,
            'Event_Type_21' => 21,
            'Event_Type_22' => 22,
            'Event_Type_23' => 23,
            'Event_Type_24' => 24,
            'Event_Type_25' => 25,
            'Event_Type_26' => 26,
            'Event_Type_27' => 27,
            'Event_Type_28' => 28,
            'Event_Type_29' => 29,
            'Event_Type_30' => 30,
            'Event_Type_31' => 31,
            'Event_Type_32' => 32,
            'Event_Type_33' => 33,
            'Event_Type_34' => 34,
            'Event_Type_35' => 35,
            'Event_Type_36' => 36,
            'Event_Type_37' => 37,
            'Event_Type_38' => 38,
            'Event_Type_39' => 39,
            'Event_Type_40' => 40,
            'Event_Type_41' => 41,
            'Event_Type_42' => 42,
            'Event_Type_43' => 43,
            'Event_Type_44' => 44,
            'Event_Type_45' => 45,
            'Event_Type_46' => 46,
            'Event_Type_47' => 47,
            'Event_Type_48' => 48,
            'Event_Type_49' => 49,
        ],

        'USA_STATE_ABBR' =>[

            "AL" => "Alabama",
            "AK" => "Alaska",
            "AS"=> "American Samoa",
            "AZ"=> "Arizona",
            "AR"=> "Arkansas",
            "CA"=> "California",
            "CO"=> "Colorado",
            "CT"=> "Connecticut",
            "DE"=> "Delaware",
            "DC"=> "District Of Columbia",
            "FM"=> "Federated States Of Micronesia",
            "GA"=> "Georgia",
            "GU"=> "Guam",
            "HI"=> "Hawaii",
            "ID"=> "Idaho",
            "IL"=> "Illinois",
            "IN"=> "Indiana",
            "IA"=> "Iowa",
            "KS"=> "Kansas",
            "KY"=> "Kentucky",
            "LA"=> "Louisiana",
            "ME"=> "Maine",
            "MH"=> "Marshall Islands",
            "MD"=> "Maryland",
            "MA"=> "Massachusetts",
            "MI"=> "Michigan",
            "MN"=> "Minnesota",
            "MS"=> "Mississippi",
            "MO"=> "Missouri",
            "MT"=> "Montana",
            "NE"=> "Nebraska",
            "NV"=> "Nevada",
            "NH"=> "New Hampshire",
            "NJ"=> "New Jersey",
            "NM"=> "New Mexico",
            "NY"=> "New York",
            "NC"=> "North Carolina",
            "ND"=> "North Dakota",
            "MP"=> "Northern Mariana Islands",
            "OH"=> "Ohio",
            "OK"=> "Oklahoma",
            "OR"=> "Oregon",
            "PW"=> "Palau",
            "PA"=> "Pennsylvania",
            "PR"=> "Puerto Rico",
            "RI"=> "Rhode Island",
            "SC"=> "South Carolina",
            "SD"=> "South Dakota",
            "TN"=> "Tennessee",
            "TX"=> "Texas",
            "UT"=> "Utah",
            "VT"=> "Vermont",
            "VI"=> "Virgin Islands",
            "VA"=> "Virginia",
            "WA"=> "Washington",
            "WV"=> "West Virginia",
            "WI"=> "Wisconsin",
            "WY"=> "Wyoming",
            "FL"=> "Florida",
    ],

    'LANGUAGES' => [
        'ENGLISH' => 'English',
        'SPANISH' => 'Spanish'
    ],

    'script_question_condition_value' => [
            'yes' => 1,
            'no' => 2,
    ],
    'script_question_condition_value_reverse' => [
        1 => 'Yes',
        2 => "No",
    ],

        // 'roles' =>

        'aws_folder' => env('AWS_FOLDER', 'app_storage/local/'),
        'api_key'  => env('API_KEY','Gu1WRSsmK7lkPwWloLl20bgiRF2dKWDA84McmB'),
        'RESTRICT_CRITICAL_EMAIL_CLIENTS'=>env("RESTRICT_CRITICAL_EMAIL_CLIENTS"),
        'TPV360_SUPPORT_EMAIL' => env('TPV360_SUPPORT_EMAIL'),
        'CLIENT_LEAD_DATA_UPLOAD_PATH' => 'client/leaddata/',
        'CLIENT_BOLT_ENERGY_E_SIGNATURE_UPLOAD_PATH' => 'client/bolt-energy/e-signature/',
        'CLIENT_BOLT_ENERGY_ACKNOWLEDGE_UPLOAD_PATH' => 'client/bolt-energy/acknowledge/',
        'GPS_LOCATION_IMAGE_UPLOAD_PATH' => 'gps_location_image/',
        'CONTRACT_PDF_UPLOAD_PATH' => 'contract/pdf/',
        'SALESCENTER_LOGO_UPLOAD_PATH' => 'salescenter/logo/',
        'TPVAGENT_PROFILE_PICTURE_UPLOAD_PATH' => 'tpvagent/profile-picture/',
        'USER_PROFILE_PICTURE_UPLOAD_PATH' => 'user/profile-picture/',
        'USER_DOCUMENTS_UPLOAD_PATH' => 'user/documents/',
        'TPV_RECEIPT_PDF_UPLOAD_PATH' => 'tpv-receipt/',
        'CRITICAL_PDF_UPLOAD_PATH' => 'user/critical_logs/pdf/',
        'TPV_RECORDING_UPLOAD_PATH' => 'recordings/',
        'CLIENT_CONSENT_RECORDING_UPLOAD_PATH' => 'consent_recordings/',
        'CLIENT_CONTRACTS_PATH' => 'contracts/',
        'CLIENT_TPV_RECEIPT_PATH' => 'tpv_receipts/',

        'GEOMAPPING_VALIDATION_DISTANCE_IN_METER' => 20,
        'CRITICAL_LOG_PDF_COUNT' => 4,

        'PHONE_NUM_VERIFICATION_OTP_TYPE' => ['VOICE' => 'voice', 'SMS' => 'sms'],
        'OUTBOUND_CALL_TYPE' => "outbound",
        'INBOUND_CALL_TYPE' => 'inbound',

        'CALL_RESCHEDULE_STATUS_ARRAY' => ['busy', 'failed', 'no-answer'],
        'CALL_COMPLETED_STATUS' => 'completed',
        'SCHEDULED_STATUS_ATTEMPTED' => 'attempted',
        'TELESALES_STATUS_TO_RESCHEDULE_CALL' => ['pending', 'hangup', 'self-verified'],
        'MAX_RESCHEDULE_CALL_COUNT' => 3,
        'MAX_RESCHEDULE_CALL_SUNRISE' => env('MAX_RESCHEDULE_CALL_SUNRISE', 3),
        'SCHEDULE_CALL_SECOND_ATTEMPT_TIME_MINS' => 2,
        'SCHEDULE_CALL_THIRD_ATTEMPT_TIME_MINS' => 2,
        'SCHEDULE_CALL_DEFAULT_ATTEMPT_TIME_MINS' => 2,
        'SCHEDULE_CALL_DELAY' => [
             2,2,2
        ],
        'SCHEDULE_CALL_DELAY_SUNRISE' => [
            2,5,10
        ],
        'SCHEDULE_CALL_TYPE_OUTBOUND_DISCONNECT' => 'outbound_disconnect',
        'SCHEDULE_PENDING_STATUS' => 'pending',
        'SCHEDULE_TASK_CREATED_STATUS' => 'task-created',
        'SCHEDULE_TASK_SKIP_STATUS' => 'skip',
        'LEAD_TYPE_VERIFIED' => 'verified',
        'LEAD_TYPE_DISCONNECTED' => 'hangup',
        'LEAD_TYPE_CANCELED' => 'cancel',
        'LEAD_TYPE_PENDING' => 'pending',
        'LEAD_TYPE_DECLINE' => 'decline',
        'LEAD_TYPE_EXPIRED' => 'expired',
        'LEAD_STATUS_SELF_VERIFIED' => 'self-verified',
        'LEAD_STATUS_FOR_EXPIRED' => ['pending','hangup','self-verified'],
        'LEAD_EXPIRY_DEFAULT_TIME' =>72, //in hours
        'PRODUCT_TYPE_LABEL' => 'product type',
        'RATE_2_LABEL' => 'rate 2',

        'TASK_TIMEOUT_ON_TWILIO' => 172800,

        'NEW_COMMON_FIELDS_FOR_SCRIPT' => [
                                          '[TPVAgent]',
                                          '[Date]',
                                          '[Time]',
                                          '[Verification Code]',
                                          '[Lead Id]',
                                          '[Channel]',
                                          '[Sales Center]',
                                          '[Sales Center Location]'
                                        ],

        'SELF_VERIFICATION_LINK_EMAIL_MODE' => 'email',
        'SELF_VERIFICATION_LINK_PHONE_MODE' => 'phone',
        'TWILIO_TASK_VOICE_CHANNEL' => 'voice',
        'TELESALES_ALERT_PROCEED_STATUS' => 'proceed',
        'TELESALES_ALERT_CANCELLED_STATUS' => 'cancelled',

        'DASHBOARD_LEAD_CATEGORIES' => [
          'good_sale' => 'Good Sale',
          'pending_leads' => 'Pending Leads',
          'bad_sale' => 'Bad Sale',
          'cancelled_leads' => 'Cancelled Leads'
        ],
        'DASHBOARD_LEAD_CATEGORIES_REVERSE' => [
            'Good Sale' => 'good_sale' ,
            'Pending Leads' => 'pending_leads' ,
            'Bad Sale' => 'bad_sale' ,
            'Cancelled Leads' => 'cancelled_leads'
          ],
        'DASHBOARD_CHANNEL_CATEGORIES' => [
          'd2d_sales' => 'D2D Sales',
          'tele_sales' => 'Tele Sales'
        ],
        'DASHBOARD_CHANNEL_CATEGORIES_FOR_DISPLAY' => [
            'd2d' => 'Door-to-Door',
            'tele' => 'Telemarketing'
          ],
        'USER_ACCESS_LEVEL' => 'salesagent',
        'ROLE_CLIENT_ADMIN' => 'client_admin',
        'ROLE_SALES_CENTER_QA' => 'sales_center_qa',
        'ROLE_GLOBAL_ADMIN' => 'admin',
        'TPVAGENT_ACCESS_LEVEL' => 'tpvagent',
        'STATUS_ACTIVE' => 'active',
        'STATUS_INACTIVE' => 'inactive',
        'DELAY_TIME_FOR_SELF_VERIFICATION_MAIL' => 120, //in seconds,

        'FORM_CHANNEL_BOTH' => 'BOTH',
        'FORM_CHANNEL_WEB' => 'WEB',
        'FORM_CHANNEL_MOBILE' => 'MOBILE',
        'DISPLAY_PHONE_NUMBER_FORMAT_10_DIGIT' => '/(\d{3})(\d{3})(\d{4})/',
        'PHONE_NUMBER_REPLACEMENT_10_DIGIT' => '$1-$2-$3',
        'DISPLAY_PHONE_NUMBER_FORMAT' => '/(.{1})(\d{3})(\d{3})(\d{4})/',
        'PHONE_NUMBER_REPLACEMENT' => '$1-$2-$3-$4',
        'PHONE_NUMBER_VALIDATION_REGEX' => '/^[\+]?1\(?[-\s ]?(\d{3})\)?[-\s ]?(\d{3})[-\s ]?(\d{4})$/',

        'IVR_INBOUND_VERIFICATION' => 5,
        'LANGUAGE_LABEL' => 'E-Signature Language',
        'ACCOUNT_NUMBER_LABEL' => 'account number',
        'ECOGOLD_PROGRAM_LABEL' => 'ecogold program',
        'ECOGOLD_PROGRAM_CODE' => 'MDSPREGUARD',
        
        'ECOGOLD_CODE_WITHOUT_SG' => ['NJSPRVAR','MDSPRVAR','MD12FIX','PASPRVAR'],
        'ECOGOLD_PROGRAM_OPTIONS' => [
            [
                'option' => 'Spring Guard',
                'selected' => false
            ]
        ],
        'PROMO_CODE_FIELD_LABEL' => 'promo code',
        'PROMO_CODE_PROGRAM' => [ 
            'GIFT' => ['MDSPREGUARD','MD12FIX'],
            'ALL' => ['MDSPRVAR','NJSPRVAR','PASPRVAR'],
            'KIWI_ENERGY' => ['OHKIWICLN','NY36MZG']
        ],

        'RRH_PROGRAM_MAPPING' =>[
            'MDSPRVAR' => ['MDSPRVAR','MD12FIX'],
            'MD12FIX'=> ['MD12FIX','MDSPRVAR'],
            'MDSPREGUARD' => ['MDSPREGUARD'],
            'KIWIEHGUARD' => ['KIWIEHGUARD'],
            'NY36MZG' => ['NY36MZG'],
            'OHKIWICLN' => ['OHKIWICLN'],
            'NJSPRVAR' => ['NJSPRVAR'],
            'PASPRVAR' => ['PASPRVAR']
        ],
        'DEFAULTS_CLIENT_ID_PERMISSION' => env('DEFAULTS_CLIENT_ID_PERMISSION', 102),

        'ECOGOLD_PROGRAM_CODE_1_KIWI_ENERGY' => 'OHKIWICLN',
        'ECOGOLD_PROGRAM_CODE_2_KIWI_ENERGY' => 'KIWIEHGUARD',
        'ECOGOLD_PROGRAM_CODE_3_KIWI_ENERGY' =>'NY36MZG',
        'KIWI_BRAND_NAME' => 'kiwi energy',
        'ANDROID_MIN_REQUIRED_VERSION' => env('ANDROID_MIN_REQUIRED_VERSION', 2.0),
        'IOS_MIN_REQUIRED_VERSION' => env('IOS_MIN_REQUIRED_VERSION', 2.0),
        'TPV_NOW_OUTBOUND_METHOD' => 6,
        'TIME_ARRAY' => [
                            "09:00 AM", "09:15 AM", "09:30 AM", "09:45 AM",
                            "10:00 AM", "10:15 AM", "10:30 AM", "10:45 AM",
                            "11:00 AM", "11:15 AM", "11:30 AM", "11:45 AM",
                            "12:00 PM", "12:15 PM", "12:30 PM", "12:45 PM",
                            "01:00 PM", "01:15 PM", "01:30 PM", "01:45 PM",
                            "01:00 PM", "01:15 PM", "01:30 PM", "01:45 PM",
                            "02:00 PM", "02:15 PM", "02:30 PM", "02:45 PM",
                            "03:00 PM", "03:15 PM", "03:30 PM", "03:45 PM",
                            "04:00 PM", "04:15 PM", "04:30 PM", "04:45 PM",
                            "05:00 PM", "05:15 PM", "05:30 PM", "05:45 PM",
                            "06:00 PM", "06:15 PM", "06:30 PM", "06:45 PM",
                            "07:00 PM", "07:15 PM", "07:30 PM", "07:45 PM",
                            "08:00 PM"
        ],


        'SCHEDULE_CALL_TYPE_SELF_TPV_CALLBACK' => 'self-tpv-callback',
        'SELF_TPV_MAX_RESCHEDULE_COUNT' => 3,
        'SELF_TPV_CALLBACK_SECOND_ATTEMPT_DELAY' => 0, // mins
        'SELF_TPV_CALLBACK_THIRD_ATTEMPT_DELAY' => 30, //mins
        'RATE_IN_CENT' => env('RATE_IN_CENT',100),

        /* RRH Client Configuration */
        'CLIENT_RRH_CLIENT_ID' => env('CLIENT_RRH_CLIENT_ID'),
        'CLIENT_RRH_FTP_HOST' => env('CLIENT_RRH_FTP_HOST'),
        'CLIENT_RRH_FTP_USERNAME' => env('CLIENT_RRH_FTP_USERNAME'),
        'CLIENT_RRH_FTP_PASSWORD' => env('CLIENT_RRH_FTP_PASSWORD'),
        'CLIENT_RRH_FTP_TRANSFER_ENROLLMENT_REPORT_ENABLED' => env('CLIENT_RRH_FTP_TRANSFER_ENROLLMENT_REPORT_ENABLED'),
        'CLIENT_RRH_FTP_TRANSFER_ENROLLMENT_REPORT_FOLDER' => env('CLIENT_RRH_FTP_TRANSFER_ENROLLMENT_REPORT_FOLDER'),
        'CLIENT_RRH_FTP_TRANSFER_RECORDINGS_ENABLED' => env('CLIENT_RRH_FTP_TRANSFER_RECORDINGS_ENABLED'),
        'CLIENT_RRH_FTP_TRANSFER_RECORDINGS_FOLDER' => env('CLIENT_RRH_FTP_TRANSFER_RECORDINGS_FOLDER'),
 
        // Bolt Enegry Client Configuration
        'CLIENT_BOLT_ENEGRY_CLIENT_ID' => env('CLIENT_BOLT_ENEGRY_CLIENT_ID'),
        'CLIENT_BOLT_ENEGRY_FTP_HOST' => env('CLIENT_BOLT_ENEGRY_FTP_HOST'),
        'CLIENT_BOLT_ENEGRY_FTP_USERNAME' => env('CLIENT_BOLT_ENEGRY_FTP_USERNAME'),
        'CLIENT_BOLT_ENEGRY_FTP_PASSWORD' => env('CLIENT_BOLT_ENEGRY_FTP_PASSWORD'),
        'CLIENT_BOLT_ENEGRY_FTP_TRANSFER_ENROLLMENT_REPORT_ENABLED' => env('CLIENT_BOLT_ENEGRY_FTP_TRANSFER_ENROLLMENT_REPORT_ENABLED'),
        'CLIENT_BOLT_ENEGRY_FTP_TRANSFER_ENROLLMENT_REPORT_FOLDER' =>env('CLIENT_BOLT_ENEGRY_FTP_TRANSFER_ENROLLMENT_REPORT_FOLDER'),
        'CLIENT_BOLT_ENEGRY_FTP_TRANSFER_RECORDINGS_ENABLED' => env('CLIENT_BOLT_ENEGRY_FTP_TRANSFER_RECORDINGS_ENABLED'),
        'CLIENT_BOLT_ENEGRY_FTP_TRANSFER_RECORDINGS_FOLDER' => env('CLIENT_BOLT_ENEGRY_FTP_TRANSFER_RECORDINGS_FOLDER'),
        'CLIENT_BOLT_ENEGRY_FTP_TRANSFER_E_SIGNATURE_FOLDER' => env('CLIENT_BOLT_ENEGRY_FTP_TRANSFER_E_SIGNATURE_FOLDER'),
        'CLIENT_BOLT_ENEGRY_FTP_TRANSFER_ACKNOWLEDGE_FOLDER' => env('CLIENT_BOLT_ENEGRY_FTP_TRANSFER_ACKNOWLEDGE_FOLDER'),
        'CLIENT_BOLT_ENEGRY_FTP_TRANSFER_CONTRACTS_FOLDER' => env('CLIENT_BOLT_ENEGRY_FTP_TRANSFER_CONTRACTS_FOLDER'),

        /* LE Client Configuration */
        'CLIENT_LE_CLIENT_ID' => env('CLIENT_LE_CLIENT_ID'),
        'CLIENT_LE_CLIENT_LEAD_WEBHOOK_URL' => env("CLIENT_LE_CLIENT_LEAD_WEBHOOK_URL"),
        'CLIENT_LE_CLIENT_WEBHOOK_FLAG' => env("CLIENT_LE_CLIENT_WEBHOOK_FLAG", false),
        
        /* MEGA Client Configuration */
        'CLIENT_MEGA_ENERGY_ID' => env('CLIENT_MEGA_ENERGY_ID'),
        'CLIENT_MEGA_ENERGY_ENROLLMENT_EXPORT_EMAILS' => env('CLIENT_MEGA_ENERGY_ENROLLMENT_EXPORT_EMAILS'),
		
        /* SUNRISE Client Configuration */
        'CLIENT_SUNRISE_CLIENT_ID' => env('CLIENT_SUNRISE_CLIENT_ID'),
        
        // Interval for  TPV_AGENTS_DASHBOARD_AUTO_REFRESH_INTERVAL
        'TPV_AGENTS_DASHBOARD_AUTO_REFRESH_INTERVAL' => 10000,
       
        
        
        'CLIENT_LE_ENERGY_ENROLLMENT_EXPORT_EMAILS' => env('CLIENT_LE_ENERGY_ENROLLMENT_EXPORT_EMAILS'),
];

$constants['CLIENT_LOGO_UPLOAD_PATH'] =  'client/logo/';

$constants['roles'] = [
            'admin' => $constants['permissions'],

            'tpv_admin' => array_except($constants['permissions'], ['edit-client-info', 'add-sales-center','view-sales-center','edit-sales-center','deactivate-sales-center','add-new-commodity','edit-commodity','delete-commodity','add-utility-provider','view-utility','edit-utility','delete-utility','add-program', 'edit-program','view-programs','bulk-upload-utility','bulk-upload-program','export-utility','export-program','deactivate-program','add-new-form', 'edit-form', 'deactivate-form', 'add-client-user','view-client-user','edit-client-user','deactivate-client-user','add-new-brand-contact','edit-brand-contact','delete-brand-contact','add-dispositions','edit-dispositions','delete-dispositions', 'view-user-roles', 'all-users', 'view-client-users', 'edit-client-users', 'add-client-user', 'delete-client-user', 'view-sales-users', 'edit-sales-users', 'add-sales-users', 'delete-sales-users', 'view-all-agents', 'view-sales-agents', 'edit-sales-agents', 'add-sales-agents', 'edit-settings','view-customer-type','add-customer-type','edit-customer-type','delete-customer-type','generate-lead-detail-report','export-lead-detail-report','filter-lead-detail-report','generate-enrollment-report','export-enrollment-report','filter-enrollment-report','generate-sales-activity-report','export-sales-activity-report','filter-sales-activity-report','generate-critical-alert-report','export-critical-alert-report','filter-critical-alert-report','copy-form','deactivate-global-admin','deactivate-client-admin','deactivate-sc-admin','deactivate-sc-qa','deactivate-sales-agent','dashboard','view-commodities','view-brand-contcts','deactivate-client','add-client','edit-permission-roles','bulk-upload-dispositions','deactivate-sc-location-admin','upload-scripts','generate-sales-agent-trail','filter-sales-agent-trail','generate-billing-report','delete-client', 'delete-sales-center', 'delete-sales-center-location', 'delete-program', 'delete-form', 'delete-client-user', 'delete-lead-detail-report', 'delete-tpv-admin', 'delete-tpv-qa', 'delete-sc-admin', 'delete-sc-qa', 'delete-sc-location-admin', 'delete-sales-agent', 'delete-tpv-agent', 'bulk-upload-sales-users','export-sales-users','view-client-settings','edit-client-settings','view-alerts','edit-alerts', 'view-do-not-enroll', 'add-do-not-enroll', 'delete-do-not-enroll', 'bulk-upload-do-not-enroll', 'export-do-not-enroll','edit-brand-info','view-brand-info']),

            'tpv_qa' => array_except($constants['permissions'], ['edit-client-info','add-sales-center', 'view-sales-center', 'edit-sales-center', 'deactivate-sales-center', 'add-new-commodity', 'edit-commodity', 'delete-commodity', 'add-utility-provider', 'view-utility', 'edit-utility', 'delete-utility', 'add-program', 'edit-program', 'view-programs', 'bulk-upload-utility','bulk-upload-program', 'export-utility','export-program', 'deactivate-program', 'add-new-form', 'edit-form', 'deactivate-form', 'add-client-user', 'view-client-user', 'edit-client-user', 'deactivate-client-user', 'add-new-brand-contact', 'edit-brand-contact', 'delete-brand-contact', 'add-dispositions', 'edit-dispositions', 'delete-dispositions', 'view-user-roles', 'all-users', 'view-client-users', 'edit-client-users', 'add-client-user', 'delete-client-user', 'edit-tpv-users', 'add-tpv-users', 'delete-tpv-users', 'view-sales-users', 'edit-sales-users', 'add-sales-users', 'delete-sales-users', 'view-all-agents', 'view-sales-agents', 'edit-sales-agents', 'add-sales-agents', 'edit-tpv-agents', 'delete-tpv-agents', 'add-tpv-agents', 'edit-settings','view-customer-type','add-customer-type','edit-customer-type','delete-customer-type','generate-lead-detail-report','export-lead-detail-report','filter-lead-detail-report','generate-enrollment-report','export-enrollment-report','filter-enrollment-report','generate-sales-activity-report','export-sales-activity-report','filter-sales-activity-report','generate-critical-alert-report','export-critical-alert-report','filter-critical-alert-report','copy-form','deactivate-global-admin','deactivate-client-admin','deactivate-tpv-admin','deactivate-tpv-qa','deactivate-sc-admin','deactivate-sc-qa','deactivate-sales-agent','deactivate-tpv-agent','dashboard','view-commodities','view-brand-contcts','edit-workflow','delete-workflow' ,'add-workflow','edit-twilio-number' ,'delete-twilio-number' ,'add-twilio-number','deactivate-client','add-client','edit-permission-roles','bulk-upload-dispositions','deactivate-sc-location-admin','upload-scripts','generate-sales-agent-trail','filter-sales-agent-trail','generate-billing-report', 'delete-client', 'delete-sales-center', 'delete-sales-center-location', 'delete-program', 'delete-form', 'delete-client-user', 'delete-lead-detail-report', 'delete-tpv-admin', 'delete-tpv-qa', 'delete-sc-admin', 'delete-sc-qa', 'delete-sc-location-admin', 'delete-sales-agent', 'delete-tpv-agent', 'bulk-upload-sales-users','export-sales-users','view-client-settings','edit-client-settings','view-alerts','edit-alerts','generate-call-detail-report', 'view-do-not-enroll', 'add-do-not-enroll', 'delete-do-not-enroll', 'bulk-upload-do-not-enroll', 'export-do-not-enroll','edit-brand-info','view-brand-info'

          ]),

            'client_admin' => array_except($constants['permissions'], [
                'all-clients',  'view-user-roles', 'all-users', 'view-tpv-users', 'edit-tpv-users', 'add-tpv-users', 'delete-tpv-users', 'view-all-agents', 'view-tpv-agents', 'edit-tpv-agents', 'delete-tpv-agents', 'add-tpv-agents', 'edit-settings','deactivate-global-admin','deactivate-tpv-admin','deactivate-tpv-qa','deactivate-tpv-agent','view-workflow','edit-workflow','delete-workflow' ,'add-workflow','edit-twilio-number' ,'delete-twilio-number' ,'add-twilio-number','deactivate-client','add-client','edit-permission-roles', 'delete-client', 'delete-sales-center', 'delete-sales-center-location', 'delete-program', 'delete-form', 'delete-client-user', 'delete-tpv-admin', 'delete-tpv-qa', 'delete-sc-admin', 'delete-sc-qa', 'delete-sc-location-admin', 'delete-sales-agent', 'delete-tpv-agent','view-client-settings','edit-client-settings', 'agent-dashboard','delete-lead-detail-report','generate-call-detail-report'
            ]),

            'sales_center_admin' => array_except($constants['permissions'], ['all-clients', 'edit-client-info',  'add-sales-center', 'deactivate-sales-center', 'add-new-commodity', 'edit-commodity', 'delete-commodity', 'add-utility-provider', 'edit-utility', 'delete-utility', 'deactivate-program', 'add-new-form', 'edit-form', 'deactivate-form', 'add-client-user', 'view-client-user', 'edit-client-user', 'deactivate-client-user', 'add-new-brand-contact', 'edit-brand-contact', 'delete-brand-contact', 'add-dispositions', 'edit-dispositions', 'delete-dispositions', 'view-user-roles', 'all-users', 'view-client-users', 'edit-client-users', 'add-client-user', 'delete-client-user', 'view-tpv-users', 'edit-tpv-users', 'add-tpv-users', 'delete-tpv-users', 'view-all-agents', 'view-tpv-agents', 'edit-tpv-agents', 'delete-tpv-agents', 'add-tpv-agents', 'edit-settings','view-customer-type','add-customer-type','edit-customer-type','delete-customer-type','copy-form','deactivate-global-admin','deactivate-client-admin','deactivate-tpv-admin','deactivate-tpv-qa','deactivate-tpv-agent','bulk-upload-utility','bulk-upload-program', 'export-utility','export-program','add-program', 'edit-program', 'view-dispositions','view-workflow' ,'edit-workflow','delete-workflow' ,'add-workflow','edit-twilio-number' ,'delete-twilio-number' ,'add-twilio-number','deactivate-client','add-client','edit-permission-roles','bulk-upload-dispositions','export-dispositions','upload-scripts','generate-billing-report', 'delete-client', 'delete-sales-center', 'delete-sales-center-location', 'delete-program', 'delete-form', 'delete-client-user', 'delete-lead-detail-report', 'delete-tpv-admin', 'delete-tpv-qa', 'delete-sc-admin', 'delete-sc-qa', 'delete-sc-location-admin', 'delete-sales-agent', 'delete-tpv-agent','view-client-settings','edit-client-settings','edit-alerts', 'agent-dashboard','generate-call-detail-report', 'view-do-not-enroll', 'add-do-not-enroll', 'delete-do-not-enroll', 'bulk-upload-do-not-enroll', 'export-do-not-enroll'
            ]),

            'sales_center_qa' => array_except($constants['permissions'], ['all-clients', 'edit-client-info',  'add-sales-center', 'deactivate-sales-center', 'add-new-commodity', 'edit-commodity', 'delete-commodity', 'add-utility-provider', 'edit-utility', 'delete-utility', 'bulk-upload-utility','bulk-upload-program', 'export-utility','export-program', 'deactivate-program', 'add-new-form', 'edit-form', 'deactivate-form', 'add-client-user', 'view-client-user', 'edit-client-user', 'deactivate-client-user', 'add-new-brand-contact', 'edit-brand-contact', 'delete-brand-contact', 'add-dispositions', 'edit-dispositions', 'delete-dispositions', 'view-user-roles', 'all-users', 'view-client-users', 'edit-client-users', 'add-client-user', 'delete-client-user', 'view-tpv-users', 'edit-tpv-users', 'add-tpv-users', 'delete-tpv-users', 'view-all-agents', 'view-tpv-agents', 'edit-tpv-agents', 'delete-tpv-agents', 'add-tpv-agents','edit-sales-center','add-program','edit-program','edit-sales-users','add-sales-users','delete-sales-users','edit-sales-agents','add-sales-agents', 'edit-settings','view-customer-type','add-customer-type','edit-customer-type','delete-customer-type','copy-form','deactivate-global-admin','deactivate-client-admin','deactivate-tpv-admin','deactivate-tpv-qa','deactivate-sc-admin','deactivate-sc-qa','deactivate-sales-agent','deactivate-tpv-agent','view-dispositions','view-workflow','edit-workflow','delete-workflow' ,'add-workflow','edit-twilio-number' ,'delete-twilio-number' ,'add-twilio-number','deactivate-client','add-client','edit-permission-roles','bulk-upload-dispositions','export-dispositions','upload-scripts', 'bulk-upload-sales-users', 'generate-billing-report' , 'delete-client', 'delete-sales-center', 'delete-sales-center-location', 'delete-program', 'delete-form', 'delete-client-user', 'delete-lead-detail-report', 'delete-tpv-admin', 'delete-tpv-qa', 'delete-sc-admin', 'delete-sc-qa', 'delete-sc-location-admin', 'delete-sales-agent', 'delete-tpv-agent','view-client-settings','edit-client-settings','edit-alerts', 'agent-dashboard','generate-call-detail-report', 'view-do-not-enroll', 'add-do-not-enroll', 'delete-do-not-enroll', 'bulk-upload-do-not-enroll', 'export-do-not-enroll'
            ]),
            'sales_center_location_admin' => array_except($constants['permissions'], ['all-clients', 'edit-client-info',  'add-sales-center', 'deactivate-sales-center', 'add-new-commodity', 'edit-commodity', 'delete-commodity', 'add-utility-provider', 'edit-utility', 'delete-utility', 'bulk-upload-utility','bulk-upload-program', 'export-utility','export-program', 'deactivate-program', 'add-new-form', 'edit-form', 'deactivate-form', 'add-client-user', 'view-client-user', 'edit-client-user', 'deactivate-client-user', 'add-new-brand-contact', 'edit-brand-contact', 'delete-brand-contact', 'add-dispositions', 'edit-dispositions', 'delete-dispositions', 'view-user-roles', 'all-users', 'view-client-users', 'edit-client-users', 'add-client-user', 'delete-client-user', 'view-tpv-users', 'edit-tpv-users', 'add-tpv-users', 'delete-tpv-users', 'view-all-agents', 'view-tpv-agents', 'edit-tpv-agents', 'delete-tpv-agents', 'add-tpv-agents','edit-sales-center','add-program','edit-program','delete-sales-users','edit-settings','view-customer-type','add-customer-type','edit-customer-type','delete-customer-type','copy-form','deactivate-global-admin','deactivate-client-admin','deactivate-tpv-admin','deactivate-tpv-qa','deactivate-sc-admin','deactivate-tpv-agent','view-dispositions','view-workflow','edit-workflow','delete-workflow' ,'add-workflow','edit-twilio-number' ,'delete-twilio-number' ,'add-twilio-number','deactivate-client','add-client','edit-permission-roles','upload-scripts', 'bulk-upload-sales-users', 'generate-billing-report', 'delete-client', 'delete-sales-center', 'delete-sales-center-location', 'delete-program', 'delete-form', 'delete-client-user', 'delete-lead-detail-report', 'delete-tpv-admin', 'delete-tpv-qa', 'delete-sc-admin', 'delete-sc-qa', 'delete-sc-location-admin', 'delete-sales-agent', 'delete-tpv-agent','view-client-settings','edit-client-settings','edit-alerts', 'agent-dashboard','generate-call-detail-report', 'view-do-not-enroll', 'add-do-not-enroll', 'delete-do-not-enroll', 'bulk-upload-do-not-enroll', 'export-do-not-enroll'
            ])
    ];
    $constants['UTC_TIME'] = '+00:00';
    $constants['AMERICA_TORONTO_TIME'] = '-05:00';
    
    

return $constants;
