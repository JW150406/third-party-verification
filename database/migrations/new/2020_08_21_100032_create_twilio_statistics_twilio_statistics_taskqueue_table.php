<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwilioStatisticsTwilioStatisticsTaskqueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_statistics_taskqueue', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('workspace_id')->nullable();
            $table->bigInteger('account_sid')->nullable();
            $table->bigInteger('task_queue_sid')->nullable();
            $table->integer('reservations_accepted')->nullable();
            $table->integer('reservations_created')->nullable();
            $table->integer('reservations_rejected')->nullable();
            $table->integer('reservations_timed_out')->nullable();
            $table->integer('tasks_moved')->nullable();
            $table->integer('tasks_deleted')->nullable();
            $table->integer('reservations_rescinded')->nullable();
            $table->integer('avg_task_acceptance_time')->nullable();
            $table->integer('wait_duration_until_canceled_avg')->nullable();
            $table->integer('wait_duration_until_canceled_min')->nullable();
            $table->integer('wait_duration_until_canceled_max')->nullable();
            $table->integer('wait_duration_until_canceled_total')->nullable();
            $table->integer('wait_duration_until_accepted_avg')->nullable();
            $table->integer('wait_duration_until_accepted_min')->nullable();
            $table->integer('wait_duration_until_accepted_max')->nullable();
            $table->integer('wait_duration_until_accepted_total')->nullable();
            $table->integer('wait_duration_in_queue_until_accepted_avg')->nullable();
            $table->integer('wait_duration_in_queue_until_accepted_min')->nullable();
            $table->integer('wait_duration_in_queue_until_accepted_max')->nullable();
            $table->integer('wait_duration_in_queue_until_accepted_total')->nullable();
            $table->integer('reservations_canceled')->nullable();
            $table->integer('tasks_completed')->nullable();
            $table->integer('tasks_entered')->nullable();
            $table->integer('tasks_canceled')->nullable();
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
        Schema::dropIfExists('twilio_statistics_taskqueue');
    }
}
