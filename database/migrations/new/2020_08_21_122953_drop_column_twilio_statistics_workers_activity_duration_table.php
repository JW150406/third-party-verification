<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnTwilioStatisticsWorkersActivityDurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_statistics_workers_activity_duration', function (Blueprint $table) {
            $table->dropColumn('realtime_friendly_name');
            $table->dropColumn('realtime_sid');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_statistics_workers_activity_duration', function (Blueprint $table) {
            $table->string('realtime_friendly_name')->nullable();
            $table->string('realtime_sid')->nullable();

        });
    }
}
