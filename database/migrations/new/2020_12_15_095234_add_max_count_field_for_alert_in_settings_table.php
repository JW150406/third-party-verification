<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMaxCountFieldForAlertInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->unsignedInteger('max_times_alert1_tele')->after('is_critical_alert8_d2d')->default(1);
            $table->unsignedInteger('max_times_alert3_tele')->after('max_times_alert1_tele')->default(3);
            $table->unsignedInteger('max_times_alert4_tele')->after('max_times_alert3_tele')->default(3);
            $table->unsignedInteger('max_times_alert1_d2d')->after('max_times_alert4_tele')->default(1);
            $table->unsignedInteger('max_times_alert3_d2d')->after('max_times_alert1_d2d')->default(3);
            $table->unsignedInteger('max_times_alert4_d2d')->after('max_times_alert3_d2d')->default(3);
            $table->unsignedInteger('interval_days_alert1_tele')->after('max_times_alert4_d2d')->nullable();
            $table->unsignedInteger('interval_days_alert2_tele')->after('interval_days_alert1_tele')->nullable();
            $table->unsignedInteger('interval_days_alert3_tele')->after('interval_days_alert2_tele')->nullable();
            $table->unsignedInteger('interval_days_alert4_tele')->after('interval_days_alert3_tele')->nullable();
            $table->unsignedInteger('interval_days_alert1_d2d')->after('interval_days_alert4_tele')->nullable();
            $table->unsignedInteger('interval_days_alert2_d2d')->after('interval_days_alert1_d2d')->nullable();
            $table->unsignedInteger('interval_days_alert3_d2d')->after('interval_days_alert2_d2d')->nullable();
            $table->unsignedInteger('interval_days_alert4_d2d')->after('interval_days_alert3_d2d')->nullable();
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
            $table->dropColumn(['max_times_alert1_tele', 'max_times_alert3_tele', 'max_times_alert4_tele', 'max_times_alert1_d2d', 'max_times_alert3_d2d', 'max_times_alert4_d2d', 'interval_days_alert1_tele', 'interval_days_alert2_tele', 'interval_days_alert3_tele', 'interval_days_alert4_tele', 'interval_days_alert1_d2d', 'interval_days_alert2_d2d', 'interval_days_alert3_d2d', 'interval_days_alert4_d2d']);
        });
    }
}
