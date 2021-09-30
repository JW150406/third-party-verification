<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDeletedAtInTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('commodities', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('commodity_units', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('clientsforms', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('dispositions', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('programs', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('salesagent_detail', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('salescenters', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('salescenterslocations', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('telesales', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('telesales_programs', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('user_locations', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('telesalesdata', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('form_scripts', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('client_twilio_workflowids', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('client_twilio_numbers', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('utilities', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('customer_types', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('script_questions', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('form_fields', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('brand_contacts', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('call_answers', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('leadmedia', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('selfverify_details', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('telesales_self_verify_exp_time', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('telesales_tmp', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('telesalesdata_tmp', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('sales_agent_activities', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('user_twilio_id', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('location_channels', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('salesagentlocations', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('critical_logs_history', function (Blueprint $table) {
            $table->softDeletes();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('commodities', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('commodity_units', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('clientsforms', function (Blueprint $table) {
             $table->dropSoftDeletes();
        });
        Schema::table('dispositions', function (Blueprint $table) {
             $table->dropSoftDeletes();
        });
        Schema::table('programs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('salesagent_detail', function (Blueprint $table) {
             $table->dropSoftDeletes();
        });
        Schema::table('salescenters', function (Blueprint $table) {
             $table->dropSoftDeletes();
        });
        Schema::table('salescenterslocations', function (Blueprint $table) {
             $table->dropSoftDeletes();
        });
        Schema::table('telesales', function (Blueprint $table) {
             $table->dropSoftDeletes();
        });
        Schema::table('telesales_programs', function (Blueprint $table) {
             $table->dropSoftDeletes();
        });
        Schema::table('user_locations', function (Blueprint $table) {
             $table->dropSoftDeletes();
        });
        Schema::table('telesalesdata', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('form_scripts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('client_twilio_workflowids', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('client_twilio_numbers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('utilities', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('customer_types', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('script_questions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('form_fields', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('brand_contacts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('call_answers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('leadmedia', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('selfverify_details', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('telesales_self_verify_exp_time', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('telesales_tmp', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('telesalesdata_tmp', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('sales_agent_activities', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('user_twilio_id', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('location_channels', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('salesagentlocations', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('critical_logs_history', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
    }
}
