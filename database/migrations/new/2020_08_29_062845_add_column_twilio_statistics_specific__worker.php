<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTwilioStatisticsSpecificWorker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_statistics_specific_Worker', function (Blueprint $table) {
            $table->integer('cumulative_reservations_completed')->nullable()->after('cumulative_reservations_canceled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_statistics_specific_Worker', function (Blueprint $table) {
            //
        });
    }
}
