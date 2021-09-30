<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnTypeTwilioStatisticsSpecificWorkerActivityDuration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_statistics_specific_Worker_activity_duration', function (Blueprint $table) {
            $table->string('Worker_id')->change();
            $table->string('cumulative_sid')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_statistics_specific_Worker_activity_duration', function (Blueprint $table) {
            //
        });
    }
}
