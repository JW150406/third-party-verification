<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAlertforToFraudAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE `fraud_alerts`  ADD `alert_for` SET('disposition','fraudalert') NULL  AFTER `type`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fraud_alerts', function (Blueprint $table) {
            $table->dropColumn('alert_for');
        });
    }
}
