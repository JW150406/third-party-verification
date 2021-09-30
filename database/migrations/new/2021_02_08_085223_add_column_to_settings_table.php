<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('is_enable_alert8_tele')->default(0)->after('is_enable_alert7_tele');
            $table->boolean('is_enable_alert9_tele')->default(0)->after('is_enable_alert8_tele');
            $table->boolean('is_enable_alert10_tele')->default(0)->after('is_enable_alert9_tele');
            $table->boolean('is_enable_alert11_tele')->default(0)->after('is_enable_alert10_tele');
            $table->boolean('is_enable_alert12_tele')->default(0)->after('is_enable_alert11_tele');
            $table->boolean('is_enable_alert9_d2d')->default(0)->after('is_enable_alert8_d2d');
            $table->boolean('is_enable_alert10_d2d')->default(0)->after('is_enable_alert9_d2d');
            $table->boolean('is_enable_alert11_d2d')->default(0)->after('is_enable_alert10_d2d');
            $table->boolean('is_enable_alert12_d2d')->default(0)->after('is_enable_alert11_d2d');
            $table->boolean('is_enable_alert13_d2d')->default(0)->after('is_enable_alert12_d2d');
            $table->unsignedInteger('interval_days_alert8_tele')->after('interval_days_alert4_tele')->nullable();
            $table->unsignedInteger('interval_days_alert9_tele')->after('interval_days_alert8_tele')->nullable();
            $table->unsignedInteger('interval_days_alert11_tele')->after('interval_days_alert9_tele')->nullable();
            $table->unsignedInteger('interval_days_alert12_tele')->after('interval_days_alert11_tele')->nullable();
            $table->unsignedInteger('interval_days_alert9_d2d')->after('interval_days_alert4_d2d')->nullable();
            $table->unsignedInteger('interval_days_alert10_d2d')->after('interval_days_alert9_d2d')->nullable();
            $table->unsignedInteger('interval_days_alert12_d2d')->after('interval_days_alert10_d2d')->nullable();
            $table->unsignedInteger('interval_days_alert13_d2d')->after('interval_days_alert12_d2d')->nullable();
            $table->unsignedInteger('is_show_agent_alert8_tele')->after('is_show_agent_alert7_tele')->default(1);
            $table->unsignedInteger('is_show_agent_alert9_tele')->after('is_show_agent_alert8_tele')->default(1);
            $table->unsignedInteger('is_show_agent_alert10_tele')->after('is_show_agent_alert9_tele')->default(1);
            $table->unsignedInteger('is_show_agent_alert11_tele')->after('is_show_agent_alert10_tele')->default(1);
            $table->unsignedInteger('is_show_agent_alert12_tele')->after('is_show_agent_alert11_tele')->default(1);
            $table->unsignedInteger('is_show_agent_alert9_d2d')->after('is_show_agent_alert8_d2d')->default(1);
            $table->unsignedInteger('is_show_agent_alert10_d2d')->after('is_show_agent_alert9_d2d')->default(1);
            $table->unsignedInteger('is_show_agent_alert11_d2d')->after('is_show_agent_alert10_d2d')->default(1);
            $table->unsignedInteger('is_show_agent_alert12_d2d')->after('is_show_agent_alert11_d2d')->default(1);
            $table->unsignedInteger('is_show_agent_alert13_d2d')->after('is_show_agent_alert12_d2d')->default(1);
            $table->boolean('is_critical_alert8_tele')->after('is_critical_alert7_tele')->default(0);
            $table->boolean('is_critical_alert9_tele')->after('is_critical_alert8_tele')->default(0);
            $table->boolean('is_critical_alert10_tele')->after('is_critical_alert9_tele')->default(0);
            $table->boolean('is_critical_alert11_tele')->after('is_critical_alert10_tele')->default(0);
            $table->boolean('is_critical_alert12_tele')->after('is_critical_alert11_tele')->default(0);
            $table->boolean('is_critical_alert9_d2d')->after('is_critical_alert8_d2d')->default(0);
            $table->boolean('is_critical_alert10_d2d')->after('is_critical_alert9_d2d')->default(0);
            $table->boolean('is_critical_alert11_d2d')->after('is_critical_alert10_d2d')->default(0);
            $table->boolean('is_critical_alert12_d2d')->after('is_critical_alert11_d2d')->default(0);
            $table->boolean('is_critical_alert13_d2d')->after('is_critical_alert12_d2d')->default(0);

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
                'is_enable_alert8_tele',
                'is_enable_alert9_tele',
                'is_enable_alert10_tele',
                'is_enable_alert11_tele',
                'is_enable_alert12_tele',
                'is_enable_alert9_d2d',
                'is_enable_alert10_d2d',
                'is_enable_alert11_d2d',
                'is_enable_alert12_d2d',
                'is_enable_alert13_d2d',
                'interval_days_alert8_tele',
                'interval_days_alert9_tele',
                'interval_days_alert11_tele',
                'interval_days_alert12_tele',
                'interval_days_alert9_d2d',
                'interval_days_alert10_d2d',
                'interval_days_alert12_d2d',
                'interval_days_alert13_d2d',
                'is_critical_alert8_tele',
                'is_critical_alert9_tele',
                'is_critical_alert10_tele',
                'is_critical_alert11_tele',
                'is_critical_alert12_tele',
                'is_critical_alert9_d2d',
                'is_critical_alert10_d2d',
                'is_critical_alert11_d2d',
                'is_critical_alert12_d2d',
                'is_critical_alert13_d2d',
                'is_show_agent_alert8_tele',
                'is_show_agent_alert9_tele',
                'is_show_agent_alert10_tele',
                'is_show_agent_alert11_tele',
                'is_show_agent_alert12_tele',
                'is_show_agent_alert9_d2d',
                'is_show_agent_alert10_d2d',
                'is_show_agent_alert11_d2d',
                'is_show_agent_alert12_d2d',
                'is_show_agent_alert13_d2d'
            ]);
        });
    }
}
