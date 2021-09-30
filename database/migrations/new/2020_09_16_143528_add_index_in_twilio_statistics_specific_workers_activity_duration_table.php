<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexInTwilioStatisticsSpecificWorkersActivityDurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_statistics_specific_Worker_activity_duration', function (Blueprint $table) {
            $table->index('workers_id','twilio_specific_workers_id');
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
            $table->dropIndex(['twilio_specific_workers_id']);
        });
    }
}
