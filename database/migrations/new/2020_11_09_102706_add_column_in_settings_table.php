<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('is_enable_d2d_app')->default(1)->after('is_enable_field_5');
            $table->boolean('is_enable_ivr')->default(1)->after('is_enable_d2d_app');
            $table->boolean('is_enable_cust_call_num')->default(1)->after('is_enable_ivr');
            $table->boolean('is_enable_agent_tpv_num')->default(1)->after('is_enable_cust_call_num');
            $table->boolean('is_enable_outbound_tpv')->default(1)->after('is_enable_agent_tpv_num');
            $table->boolean('is_enable_self_tpv_tele')->default(1)->after('is_enable_outbound_tpv');
            $table->boolean('is_enable_self_tpv_d2d')->default(1)->after('is_enable_self_tpv_tele');
            $table->boolean('is_enable_contract_tele')->default(1)->after('is_enable_self_tpv_d2d');
            $table->boolean('is_enable_contract_d2d')->default(1)->after('is_enable_contract_tele');
            $table->boolean('is_enable_clone_lead')->default(1)->after('is_enable_contract_d2d');
            $table->boolean('is_enable_lead_view_page')->default(1)->after('is_enable_clone_lead');
            $table->boolean('is_enable_hunt_group')->default(1)->after('is_enable_lead_view_page');
            $table->boolean('is_enable_recording')->default(1)->after('is_enable_hunt_group');
            $table->boolean('is_enable_agent_time_clock')->default(1)->after('is_enable_recording');
            $table->boolean('is_enable_alert_tele')->default(1)->after('is_enable_agent_time_clock');
            $table->boolean('is_enable_alert1_tele')->default(1)->after('is_enable_alert_tele');
            $table->boolean('is_enable_alert2_tele')->default(1)->after('is_enable_alert1_tele');
            $table->boolean('is_enable_alert3_tele')->default(1)->after('is_enable_alert2_tele');
            $table->boolean('is_enable_alert4_tele')->default(1)->after('is_enable_alert3_tele');
            $table->boolean('is_enable_alert5_tele')->default(1)->after('is_enable_alert4_tele');
            $table->boolean('is_enable_alert6_tele')->default(1)->after('is_enable_alert5_tele');
            $table->boolean('is_enable_alert7_tele')->default(1)->after('is_enable_alert6_tele');
            $table->boolean('is_enable_alert_d2d')->default(1)->after('is_enable_alert7_tele');
            $table->boolean('is_enable_alert1_d2d')->default(1)->after('is_enable_alert_d2d');
            $table->boolean('is_enable_alert2_d2d')->default(1)->after('is_enable_alert1_d2d');
            $table->boolean('is_enable_alert3_d2d')->default(1)->after('is_enable_alert2_d2d');
            $table->boolean('is_enable_alert4_d2d')->default(1)->after('is_enable_alert3_d2d');
            $table->boolean('is_enable_alert5_d2d')->default(1)->after('is_enable_alert4_d2d');
            $table->boolean('is_enable_alert6_d2d')->default(1)->after('is_enable_alert5_d2d');
            $table->boolean('is_enable_alert7_d2d')->default(1)->after('is_enable_alert6_d2d');
            $table->boolean('is_enable_alert8_d2d')->default(1)->after('is_enable_alert7_d2d');
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
            $table->dropColumn([
                'is_enable_d2d_app',
                'is_enable_ivr',
                'is_enable_cust_call_num',
                'is_enable_agent_tpv_num',
                'is_enable_outbound_tpv',
                'is_enable_self_tpv_tele',
                'is_enable_self_tpv_d2d',
                'is_enable_contract_tele',
                'is_enable_contract_d2d',
                'is_enable_clone_lead',
                'is_enable_lead_view_page',
                'is_enable_hunt_group',
                'is_enable_agent_time_clock',
                'is_enable_recording',
                'is_enable_alert_tele',
                'is_enable_alert1_tele',
                'is_enable_alert2_tele',
                'is_enable_alert3_tele',
                'is_enable_alert4_tele',
                'is_enable_alert5_tele',
                'is_enable_alert6_tele',
                'is_enable_alert7_tele',
                'is_enable_alert_d2d',
                'is_enable_alert1_d2d',
                'is_enable_alert2_d2d',
                'is_enable_alert3_d2d',
                'is_enable_alert4_d2d',
                'is_enable_alert5_d2d',
                'is_enable_alert6_d2d',
                'is_enable_alert7_d2d',
                'is_enable_alert8_d2d',
            ]);
        });
    }
}
