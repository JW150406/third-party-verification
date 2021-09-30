<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewEnumTypeForScriptforInFormScriptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_scripts', function (Blueprint $table) {
            DB::statement("ALTER TABLE `form_scripts` CHANGE `scriptfor` `scriptfor` ENUM('salesagentintro','leadcreation','customer_verification','agent_not_found','closing','lead_not_found','after_lead_decline','customer_call_in_verification','self_verification','identity_verification','ivr_tpv_verification')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_scripts', function (Blueprint $table) {
            DB::statement("ALTER TABLE `form_scripts` CHANGE `scriptfor` `scriptfor` ENUM('salesagentintro','leadcreation','customer_verification','agent_not_found','closing','lead_not_found','after_lead_decline','customer_call_in_verification','self_verification','identity_verification')");
        });
    }
}
