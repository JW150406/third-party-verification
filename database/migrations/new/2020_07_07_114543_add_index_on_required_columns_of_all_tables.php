<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexOnRequiredColumnsOfAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('ALTER TABLE `active_calls` ADD INDEX(`agent_id`, `conference_id`);');
        \DB::statement('ALTER TABLE `brand_contacts` ADD INDEX(`client_id`);');
        \DB::statement('ALTER TABLE `call_answers` ADD INDEX(`lead_id`,`form_id`, `client_id`, `tpv_agent_id`, `sales_agent_id`, `language`, `created_at`);');
        \DB::statement('ALTER TABLE `clients` ADD INDEX(`status`, `created_at`);');
        \DB::statement('ALTER TABLE `clientsforms` ADD INDEX(`client_id`, `utility_id`, `channel`, `status`, `created_at`);');
        \DB::statement('ALTER TABLE `client_twilio_numbers` ADD INDEX(`type`, `added_by`);');
        \DB::statement('ALTER TABLE `client_twilio_workflowids` ADD INDEX(`client_id`, `workflow_id`);');
        \DB::statement('ALTER TABLE `commodities` ADD INDEX(`client_id`, `status`, `created_at`);');
        \DB::statement('ALTER TABLE `commodity_units` ADD INDEX(`commodity_id`);');
        \DB::statement('ALTER TABLE `critical_logs_history` ADD INDEX(`tmp_lead_id`, `created_at`, `lead_status`);');
        \DB::statement('ALTER TABLE `customer_types` ADD INDEX(`client_id`, `created_at`);');
        \DB::statement('ALTER TABLE `dispositions` ADD INDEX(`type`, `client_id`, `created_at`, `status`);');
        \DB::statement('ALTER TABLE `form_commodities` ADD INDEX(`form_id`, `commodity_id`);');
        \DB::statement('ALTER TABLE `form_fields` ADD INDEX(`form_id`, `type`);');
        \DB::statement('ALTER TABLE `form_scripts` ADD INDEX(`client_id`, `form_id`, `scriptfor`, `language`);');
        \DB::statement('ALTER TABLE `leadmedia` ADD INDEX(`type`);');
        \DB::statement('ALTER TABLE `permission_access_levels` ADD INDEX(`permission_id`, `access_level`);');
        \DB::statement('ALTER TABLE `permission_role` ADD INDEX(`permission_id`);');
        \DB::statement('ALTER TABLE `phonenumberverification` ADD INDEX(`verifiedby`);');
        \DB::statement('ALTER TABLE `programs` ADD INDEX(`code`, `client_id`, `created_by`, `created_at`, `utility_id`, `customer_type_id`, `status`);');
        \DB::statement('ALTER TABLE `role_user` ADD INDEX(`user_id`);');
        \DB::statement('ALTER TABLE `salesagent_detail` ADD INDEX(`added_by`, `location_id`, `agent_type`, `created_at`);');
        \DB::statement('ALTER TABLE `salescenters` ADD INDEX(`created_by`, `status`, `created_at`);');
        \DB::statement('ALTER TABLE `salescenterslocations` ADD INDEX(`created_by`, `created_at`, `status`);');
        \DB::statement('ALTER TABLE `sales_agent_activities` ADD INDEX(`agent_id`, `activity_type`, `created_at`);');
        \DB::statement('ALTER TABLE `script_questions` ADD INDEX(`client_id`, `form_id`, `script_id`, `created_by`, `created_at`, `position`, `is_introductionary`);');
        \DB::statement('ALTER TABLE `selfverify_details` ADD INDEX(`telesale_id`, `created_at`);');
        \DB::statement('ALTER TABLE `self_verification_allowed_zipcodes` ADD INDEX(`zipcode_id`);');
        \DB::statement('ALTER TABLE `telesales` ADD INDEX(`client_id`, `form_id`, `user_id`, `created_at`, `reviewed_by`, `disposition_id`, `status`, `verification_method`, `reviewed_at`, `alert_status`);');
        \DB::statement('ALTER TABLE `telesalesdata` ADD INDEX(`field_id`);');
        \DB::statement('ALTER TABLE `telesalesdata_tmp` ADD INDEX(`field_id`);');
        \DB::statement('ALTER TABLE `telesales_programs` ADD INDEX(`telesale_id`);');
        \DB::statement('ALTER TABLE `telesales_programs` ADD INDEX(`program_id`);');
        \DB::statement('ALTER TABLE `telesales_self_verify_exp_time` ADD INDEX(`telesale_id`);');
        \DB::statement('ALTER TABLE `telesales_self_verify_exp_time` ADD INDEX(`verification_mode`);');
        // \DB::statement('ALTER TABLE `telesales_tmp` ADD INDEX( `client_id, `form_id`, `user_id`, `reviewed_by`, `disposition_id`, `status`, `is_proceed`, `created_at`);');
        \DB::statement('ALTER TABLE `telesales_zipcodes` ADD INDEX( `telesale_id`, `zipcode_id`);');
        \DB::statement('ALTER TABLE `telesale_schedule_call` ADD INDEX( `call_immediately`, `call_lang`, `attempt_no`, `dial_status`, `schedule_status`, `created_at`);');
        \DB::statement('ALTER TABLE `text_email_statistics` ADD INDEX( `type`);');
        \DB::statement('ALTER TABLE `twilio_connected_devices` ADD INDEX( `user_id`);');
        \DB::statement('ALTER TABLE `users` ADD INDEX( `created_at`, `access_level`, `status`, `location_id`, `is_block`);');
        \DB::statement('ALTER TABLE `user_twilio_id` ADD INDEX( `user_id`, `workflow_id`);');
        \DB::statement('ALTER TABLE `utilities` ADD INDEX( `utilityname`, `created_at`, `client_id`, `created_by`, `commodity_id`);');
        \DB::statement('ALTER TABLE `zip_codes` ADD INDEX( `zipcode`, `state`);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('ALTER TABLE `active_calls` DROP INDEX `agent_id`;');
        \DB::statement('ALTER TABLE `active_calls` DROP INDEX `conference_id`;');
        \DB::statement('ALTER TABLE `brand_contacts` DROP INDEX `client_id`;');
        \DB::statement('ALTER TABLE `call_answers` DROP INDEX `lead_id`;');
        \DB::statement('ALTER TABLE `call_answers` DROP INDEX `form_id`;');
        \DB::statement('ALTER TABLE `call_answers` DROP INDEX `client_id`;');
        \DB::statement('ALTER TABLE `call_answers` DROP INDEX `tpv_agent_id`;');
        \DB::statement('ALTER TABLE `call_answers` DROP INDEX `sales_agent_id`;');
        \DB::statement('ALTER TABLE `call_answers` DROP INDEX `language`;');
        \DB::statement('ALTER TABLE `call_answers` DROP INDEX `created_at`;');
        \DB::statement('ALTER TABLE `clients` DROP INDEX `status`;');
        \DB::statement('ALTER TABLE `clients` DROP INDEX `created_at`;');
        \DB::statement('ALTER TABLE `clientsforms` DROP INDEX `client_id`;');
        \DB::statement('ALTER TABLE `clientsforms` DROP INDEX `utility_id`;');
        \DB::statement('ALTER TABLE `clientsforms` DROP INDEX `channel`;');
        \DB::statement('ALTER TABLE `clientsforms` DROP INDEX `status`;');
        \DB::statement('ALTER TABLE `clientsforms` DROP INDEX `created_at`;');
        \DB::statement('ALTER TABLE `client_twilio_numbers` DROP INDEX `type`;');
        \DB::statement('ALTER TABLE `client_twilio_numbers` DROP INDEX `added_by`;');
        \DB::statement('ALTER TABLE `client_twilio_workflowids` DROP INDEX `client_id`;');
        \DB::statement('ALTER TABLE `client_twilio_workflowids` DROP INDEX `workflow_id`;');
        \DB::statement('ALTER TABLE `commodities` DROP INDEX `client_id`;');
        \DB::statement('ALTER TABLE `commodities` DROP INDEX `status`;');
        \DB::statement('ALTER TABLE `commodities` DROP INDEX `created_at`;');
        \DB::statement('ALTER TABLE `commodity_units` DROP INDEX `commodity_id`;');
        \DB::statement('ALTER TABLE `critical_logs_history` DROP INDEX `tmp_lead_id`;');
        \DB::statement('ALTER TABLE `critical_logs_history` DROP INDEX `created_at`;');
        \DB::statement('ALTER TABLE `critical_logs_history` DROP INDEX `lead_status`;');
        \DB::statement('ALTER TABLE `customer_types` DROP INDEX `client_id`;');
        \DB::statement('ALTER TABLE `customer_types` DROP INDEX `created_at`;');
        \DB::statement('ALTER TABLE `dispositions` DROP INDEX `type`;');
        \DB::statement('ALTER TABLE `dispositions` DROP INDEX `client_id`;');
        \DB::statement('ALTER TABLE `dispositions` DROP INDEX `created_at`;');
        \DB::statement('ALTER TABLE `dispositions` DROP INDEX `status`;');
        \DB::statement('ALTER TABLE `form_commodities` DROP INDEX `form_id`;');
        \DB::statement('ALTER TABLE `form_commodities` DROP INDEX `commodity_id`;');
        \DB::statement('ALTER TABLE `form_fields` DROP INDEX `form_id`;');
        \DB::statement('ALTER TABLE `form_fields` DROP INDEX `type`;');
        \DB::statement('ALTER TABLE `form_scripts` DROP INDEX `client_id`;');
        \DB::statement('ALTER TABLE `form_scripts` DROP INDEX `form_id`;');
        \DB::statement('ALTER TABLE `form_scripts` DROP INDEX `scriptfor`;');
        \DB::statement('ALTER TABLE `form_scripts` DROP INDEX `language`;');
        \DB::statement('ALTER TABLE `leadmedia` DROP INDEX `type`;');
        \DB::statement('ALTER TABLE `permission_access_levels` DROP INDEX `permission_id`;');
        \DB::statement('ALTER TABLE `permission_access_levels` DROP INDEX `access_level`;');
        \DB::statement('ALTER TABLE `permission_role` DROP INDEX `permission_id`;');
        \DB::statement('ALTER TABLE `phonenumberverification` DROP INDEX `verifiedby`;');
        \DB::statement('ALTER TABLE `programs` DROP INDEX `code`;');
        \DB::statement('ALTER TABLE `programs` DROP INDEX `client_id`;');
        \DB::statement('ALTER TABLE `programs` DROP INDEX `created_by`;');
        \DB::statement('ALTER TABLE `programs` DROP INDEX `created_at`;');
        \DB::statement('ALTER TABLE `programs` DROP INDEX `utility_id`;');
        \DB::statement('ALTER TABLE `programs` DROP INDEX `customer_type_id`;');
        \DB::statement('ALTER TABLE `programs` DROP INDEX `status`;');
        \DB::statement('ALTER TABLE `role_user` DROP INDEX `user_id`;');
        \DB::statement('ALTER TABLE `salesagent_detail` DROP INDEX `added_by`;');
        \DB::statement('ALTER TABLE `salesagent_detail` DROP INDEX `location_id`;');
        \DB::statement('ALTER TABLE `salesagent_detail` DROP INDEX `agent_type`;');
        \DB::statement('ALTER TABLE `salesagent_detail` DROP INDEX `created_at`;');
        \DB::statement('ALTER TABLE `salescenters` DROP INDEX `created_by`;');
        \DB::statement('ALTER TABLE `salescenters` DROP INDEX `status`;');
        \DB::statement('ALTER TABLE `salescenters` DROP INDEX `created_at`;');
        \DB::statement('ALTER TABLE `salescenterslocations` DROP INDEX `created_by`;');
        \DB::statement('ALTER TABLE `salescenterslocations` DROP INDEX `created_at`;');
        \DB::statement('ALTER TABLE `salescenterslocations` DROP INDEX `status`;');
        \DB::statement('ALTER TABLE `sales_agent_activities` DROP INDEX `agent_id`;');
        \DB::statement('ALTER TABLE `sales_agent_activities` DROP INDEX `activity_type`;');
        \DB::statement('ALTER TABLE `sales_agent_activities` DROP INDEX `created_at`;');
        \DB::statement('ALTER TABLE `script_questions` DROP INDEX `client_id`;');
        \DB::statement('ALTER TABLE `script_questions` DROP INDEX `form_id`;');
        \DB::statement('ALTER TABLE `script_questions` DROP INDEX `script_id`;');
        \DB::statement('ALTER TABLE `script_questions` DROP INDEX `created_by`;');
        \DB::statement('ALTER TABLE `script_questions` DROP INDEX `created_at`;');
        \DB::statement('ALTER TABLE `script_questions` DROP INDEX `position`;');
        \DB::statement('ALTER TABLE `script_questions` DROP INDEX `is_introductionary`;');
        \DB::statement('ALTER TABLE `selfverify_details` DROP INDEX `telesale_id`;');
        \DB::statement('ALTER TABLE `selfverify_details` DROP INDEX `created_at`;');
        \DB::statement('ALTER TABLE `self_verification_allowed_zipcodes` DROP INDEX `zipcode_id`;');
        \DB::statement('ALTER TABLE `telesales` DROP INDEX `client_id`;');
        \DB::statement('ALTER TABLE `telesales` DROP INDEX `form_id`;');
        \DB::statement('ALTER TABLE `telesales` DROP INDEX `user_id`;');
        \DB::statement('ALTER TABLE `telesales` DROP INDEX `created_at`;');
        \DB::statement('ALTER TABLE `telesales` DROP INDEX `reviewed_by`;');
        \DB::statement('ALTER TABLE `telesales` DROP INDEX `disposition_id`;');
        \DB::statement('ALTER TABLE `telesales` DROP INDEX `status`;');
        \DB::statement('ALTER TABLE `telesales` DROP INDEX `verification_method`;');
        \DB::statement('ALTER TABLE `telesales` DROP INDEX `reviewed_at`;');
        \DB::statement('ALTER TABLE `telesales` DROP INDEX `alert_status`;');
        \DB::statement('ALTER TABLE `telesalesdata` DROP INDEX `field_id`;');
        \DB::statement('ALTER TABLE `telesalesdata_tmp` DROP INDEX `field_id`;');
        \DB::statement('ALTER TABLE `telesales_programs` DROP INDEX `telesale_id`;');
        \DB::statement('ALTER TABLE `telesales_programs` DROP INDEX `program_id`;');
        \DB::statement('ALTER TABLE `telesales_self_verify_exp_time` DROP INDEX `telesale_id`;');
        \DB::statement('ALTER TABLE `telesales_self_verify_exp_time` DROP INDEX `verification_mode`;');
        \DB::statement('ALTER TABLE `telesales_tmp` DROP INDEX `client_id;');
        \DB::statement('ALTER TABLE `telesales_tmp` DROP INDEX `form_id`;');
        \DB::statement('ALTER TABLE `telesales_tmp` DROP INDEX `user_id`;');
        \DB::statement('ALTER TABLE `telesales_tmp` DROP INDEX `reviewed_by`;');
        \DB::statement('ALTER TABLE `telesales_tmp` DROP INDEX `disposition_id`;');
        \DB::statement('ALTER TABLE `telesales_tmp` DROP INDEX `status`;');
        \DB::statement('ALTER TABLE `telesales_tmp` DROP INDEX `is_proceed`;');
        \DB::statement('ALTER TABLE `telesales_tmp` DROP INDEX `created_at`;');
        \DB::statement('ALTER TABLE `telesales_zipcodes` DROP INDEX `telesale_id`;');
        \DB::statement('ALTER TABLE `telesales_zipcodes` DROP INDEX `zipcode_id`;');
        \DB::statement('ALTER TABLE `telesale_schedule_call` DROP INDEX `call_immediately`;');
        \DB::statement('ALTER TABLE `telesale_schedule_call` DROP INDEX `call_lang`;');
        \DB::statement('ALTER TABLE `telesale_schedule_call` DROP INDEX `attempt_no`;');
        \DB::statement('ALTER TABLE `telesale_schedule_call` DROP INDEX `dial_status`;');
        \DB::statement('ALTER TABLE `telesale_schedule_call` DROP INDEX `schedule_status`;');
        \DB::statement('ALTER TABLE `telesale_schedule_call` DROP INDEX `created_at`;');
        \DB::statement('ALTER TABLE `text_email_statistics` DROP INDEX `type`;');
        \DB::statement('ALTER TABLE `twilio_connected_devices` DROP INDEX `user_id`;');
        \DB::statement('ALTER TABLE `users` DROP INDEX `created_at`;');
        \DB::statement('ALTER TABLE `users` DROP INDEX `access_level`;');
        \DB::statement('ALTER TABLE `users` DROP INDEX `status`;');
        \DB::statement('ALTER TABLE `users` DROP INDEX `location_id`;');
        \DB::statement('ALTER TABLE `users` DROP INDEX `is_block`;');
        \DB::statement('ALTER TABLE `user_twilio_id` DROP INDEX `user_id`;');
        \DB::statement('ALTER TABLE `user_twilio_id` DROP INDEX `workflow_id`;');
        \DB::statement('ALTER TABLE `utilities` DROP INDEX `utilityname`;');
        \DB::statement('ALTER TABLE `utilities` DROP INDEX `created_at`;');
        \DB::statement('ALTER TABLE `utilities` DROP INDEX `client_id`;');
        \DB::statement('ALTER TABLE `utilities` DROP INDEX `created_by`;');
        \DB::statement('ALTER TABLE `utilities` DROP INDEX `commodity_id`;');
        \DB::statement('ALTER TABLE `zip_codes` DROP INDEX `zipcode`;');
        \DB::statement('ALTER TABLE `zip_codes` DROP INDEX `state`;');
    }
}
