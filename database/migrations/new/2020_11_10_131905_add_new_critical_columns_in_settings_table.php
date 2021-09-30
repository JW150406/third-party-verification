<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewCriticalColumnsInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('is_critical_alert1_tele')->default(0)->after('is_enable_alert8_d2d');
            $table->boolean('is_critical_alert2_tele')->default(0)->after('is_critical_alert1_tele');
            $table->boolean('is_critical_alert3_tele')->default(0)->after('is_critical_alert2_tele');
            $table->boolean('is_critical_alert4_tele')->default(0)->after('is_critical_alert3_tele');
            $table->boolean('is_critical_alert5_tele')->default(0)->after('is_critical_alert4_tele');
            $table->boolean('is_critical_alert6_tele')->default(0)->after('is_critical_alert5_tele');
            $table->boolean('is_critical_alert7_tele')->default(1)->after('is_critical_alert6_tele');
            $table->boolean('is_critical_alert1_d2d')->default(0)->after('is_critical_alert7_tele');
            $table->boolean('is_critical_alert2_d2d')->default(0)->after('is_critical_alert1_d2d');
            $table->boolean('is_critical_alert3_d2d')->default(0)->after('is_critical_alert2_d2d');
            $table->boolean('is_critical_alert4_d2d')->default(0)->after('is_critical_alert3_d2d');
            $table->boolean('is_critical_alert5_d2d')->default(0)->after('is_critical_alert4_d2d');
            $table->boolean('is_critical_alert6_d2d')->default(0)->after('is_critical_alert5_d2d');
            $table->boolean('is_critical_alert7_d2d')->default(1)->after('is_critical_alert6_d2d');
            $table->boolean('is_critical_alert8_d2d')->default(1)->after('is_critical_alert7_d2d');
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
                'is_critical_alert1_tele',
                'is_critical_alert2_tele',
                'is_critical_alert3_tele',
                'is_critical_alert4_tele',
                'is_critical_alert5_tele',
                'is_critical_alert6_tele',
                'is_critical_alert7_tele',
                'is_critical_alert1_d2d',
                'is_critical_alert2_d2d',
                'is_critical_alert3_d2d',
                'is_critical_alert4_d2d',
                'is_critical_alert5_d2d',
                'is_critical_alert6_d2d',
                'is_critical_alert7_d2d',
                'is_critical_alert8_d2d',
            ]);
        });
    }
}
