<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwilioStatisticsWorkflowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_statistics_workflow', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('workflow_id')->nullable();
            $table->bigInteger('workspace_id')->nullable();
            $table->bigInteger('account_sid')->nullable(); 
            $table->integer('cumulative_avg_task_acceptance_time')->nullable();
            $table->integer('cumulative_reservations_accepted')->nullable();
            $table->integer('cumulative_reservations_rejected')->nullable();
            $table->integer('cumulative_reservations_timed_out')->nullable();
            $table->integer('cumulative_tasks_canceled')->nullable();
            $table->integer('cumulative_tasks_entered')->nullable();
            $table->integer('cumulative_tasks_moved')->nullable();
            $table->integer('cumulative_tasks_timed_out_in_workflow')->nullable();
            $table->integer('realtime_longest_task_waiting_age')->nullable();
            $table->bigInteger('realtime_longest_task_waiting_sid')->nullable();     
            $table->integer('realtime_task_assigned')->nullable();
            $table->integer('realtime_task_pending')->nullable();
            $table->integer('realtime_task_reserved')->nullable();
            $table->integer('realtime_task_wrapping')->nullable();
            $table->integer('realtime_total_tasks')->nullable();
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
        Schema::dropIfExists('twilio_statistics_workflow');
    }
}
