<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteColumnIntoTwilioStatisticsTaskquue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_statistics_taskqueue', function (Blueprint $table) {
            $table->dropColumn('wait_duration_in_queue_until_accepted_avg');
            $table->dropColumn('wait_duration_in_queue_until_accepted_min');
            $table->dropColumn('wait_duration_in_queue_until_accepted_max');
            $table->dropColumn('wait_duration_in_queue_until_accepted_total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_statistics_taskqueue', function (Blueprint $table) {
            //
        });
    }
}
