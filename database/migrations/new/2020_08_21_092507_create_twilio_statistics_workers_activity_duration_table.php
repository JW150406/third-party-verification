<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwilioStatisticsWorkersActivityDurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_statistics_workers_activity_duration', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('workspace_id')->nullable();
            $table->string('cumulative_activity_name')->nullable();
            $table->integer('cumulative_maxtime')->nullable();
            $table->integer('cumulative_mintime')->nullable();
            $table->integer('cumulative_totaltime')->nullable();
            $table->integer('cumulative_avgtime')->nullable();
            $table->bigInteger('cumulative_sid')->nullable();
            $table->integer('realtime_workers')->nullable();
            $table->string('realtime_friendly_name')->nullable();
            $table->bigInteger('realtime_sid')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twilio_statistics_workers_activity_duration');
    }
}
