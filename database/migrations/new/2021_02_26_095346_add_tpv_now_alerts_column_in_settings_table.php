<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTpvNowAlertsColumnInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->unsignedInteger('max_times_alert10_tele')->after('max_times_alert4_tele')->default(3);
            $table->unsignedInteger('max_times_alert11_d2d')->after('max_times_alert4_d2d')->default(3);
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
            $table->dropColumn(['max_times_alert10_tele', 'max_times_alert11_d2d']);
        });
    }
}
