<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewEnumTypeForScriptsforColumnInFormScriptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `form_scripts` CHANGE `scriptfor` `scriptfor` ENUM('salesagentintro','leadcreation','customer_verification','agent_not_found','closing','lead_not_found','after_lead_decline','customer_call_in_verification')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `form_scripts` CHANGE `scriptfor` `scriptfor` ENUM('salesagentintro','leadcreation','customer_verification','agent_not_found','closing','lead_not_found','after_lead_decline')");
    }
}
