<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwilioLeadCallDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_lead_call_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('task_id')->nullable();
            $table->integer('lead_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->string('call_id')->nullable();
            $table->string('worker_call_id')->nullable();
            $table->string('worker_id')->nullable();
            $table->string('call_type')->nullable();
            $table->string('call_duration')->nullable();
            $table->string('recording_url')->nullable();
            $table->string('twilio_recording_url')->nullable();
            $table->timestamp('task_created_time')->nullable();
            $table->timestamp('task_wrapup_start_time')->nullable();
            $table->timestamp('task_completed_time')->nullable();
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
        Schema::dropIfExists('twilio_lead_call_details');
    }
}
