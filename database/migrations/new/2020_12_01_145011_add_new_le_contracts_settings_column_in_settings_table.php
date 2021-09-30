<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewLeContractsSettingsColumnInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('is_enable_send_contract_after_lead_verify_tele')->default(0)->after('is_enable_contract_d2d');
            $table->boolean('is_enable_send_contract_after_lead_verify_d2d')->default(0)->after('is_enable_send_contract_after_lead_verify_tele');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['is_enable_send_contract_after_lead_verify_tele','is_enable_send_contract_after_lead_verify_d2d']);
        });
    }
}