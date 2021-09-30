<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwilioStatisticsWorkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_statistics_workers', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('workspace_id')->nullable();
            $table->bigInteger('account_id')->nullable();
            $table->integer('cumulative_reservations_created')->nullable();
            $table->integer('cumulative_reservations_accepted')->nullable();
            $table->integer('cumulative_reservations_rejected')->nullable();
            $table->integer('cumulative_reservations_timed_out')->nullable();
            $table->integer('cumulative_reservations_canceled')->nullable();
            $table->integer('cumulative_reservations_rescinded')->nullable();
            $table->timestamp('cumulative_start_time')->nullable();
            $table->timestamp('cumulative_end_time')->nullable();
            $table->integer('realtime_total_workers')->nullable();
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
        Schema::dropIfExists('twilio_statistics_workers');
    }
}
