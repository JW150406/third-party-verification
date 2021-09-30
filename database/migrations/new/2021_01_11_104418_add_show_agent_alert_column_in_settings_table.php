<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShowAgentAlertColumnInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->unsignedInteger('is_show_agent_alert1_tele')->after('is_critical_alert8_d2d')->default(1);
            $table->unsignedInteger('is_show_agent_alert2_tele')->after('is_show_agent_alert1_tele')->default(1);
            $table->unsignedInteger('is_show_agent_alert3_tele')->after('is_show_agent_alert2_tele')->default(1);
            $table->unsignedInteger('is_show_agent_alert4_tele')->after('is_show_agent_alert3_tele')->default(1);
            $table->unsignedInteger('is_show_agent_alert5_tele')->after('is_show_agent_alert4_tele')->default(1);
            $table->unsignedInteger('is_show_agent_alert6_tele')->after('is_show_agent_alert5_tele')->default(1);
            $table->unsignedInteger('is_show_agent_alert7_tele')->after('is_show_agent_alert6_tele')->default(1);
            $table->unsignedInteger('is_show_agent_alert1_d2d')->after('is_show_agent_alert7_tele')->default(1);
            $table->unsignedInteger('is_show_agent_alert2_d2d')->after('is_show_agent_alert1_d2d')->default(1);
            $table->unsignedInteger('is_show_agent_alert3_d2d')->after('is_show_agent_alert2_d2d')->default(1);
            $table->unsignedInteger('is_show_agent_alert4_d2d')->after('is_show_agent_alert3_d2d')->default(1);
            $table->unsignedInteger('is_show_agent_alert5_d2d')->after('is_show_agent_alert4_d2d')->default(1);
            $table->unsignedInteger('is_show_agent_alert6_d2d')->after('is_show_agent_alert5_d2d')->default(1);
            $table->unsignedInteger('is_show_agent_alert7_d2d')->after('is_show_agent_alert6_d2d')->default(1);
            $table->unsignedInteger('is_show_agent_alert8_d2d')->after('is_show_agent_alert7_d2d')->default(1);
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
            $table->dropColumn(['is_show_agent_alert1_tele', 'is_show_agent_alert2_tele', 'is_show_agent_alert3_tele', 'is_show_agent_alert4_tele', 'is_show_agent_alert5_tele', 'is_show_agent_alert6_tele', 'is_show_agent_alert7_tele', 'is_show_agent_alert1_d2d', 'is_show_agent_alert2_d2d', 'is_show_agent_alert3_d2d', 'is_show_agent_alert4_d2d', 'is_show_agent_alert5_d2d', 'is_show_agent_alert6_d2d', 'is_show_agent_alert7_d2d', 'is_show_agent_alert8_d2d']);
        });
    }
}
