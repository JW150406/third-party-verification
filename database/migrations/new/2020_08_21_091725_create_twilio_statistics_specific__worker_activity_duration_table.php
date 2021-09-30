<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwilioStatisticsSpecificWorkerActivityDurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_statistics_specific_Worker_activity_duration', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('Worker_id')->nullable();
            $table->string('cumulative_activity_name')->nullable();
            $table->integer('cumulative_maxtime')->nullable();
            $table->integer('cumulative_mintime')->nullable();
            $table->integer('cumulative_totaltime')->nullable();
            $table->integer('cumulative_avgtime')->nullable();
            $table->bigInteger('cumulative_sid')->nullable();
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
        Schema::dropIfExists('twilio_statistics_specific_Worker_activity_duration');
    }
}
