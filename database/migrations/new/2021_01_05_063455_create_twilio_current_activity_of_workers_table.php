<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwilioCurrentActivityOfWorkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_current_activity_of_workers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('worker_id')->nullable();
            $table->string('worker_activity_id')->nullable();
            $table->string('worker_activity_name')->nullable();
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
        Schema::dropIfExists('twilio_current_activity_of_workers');
    }
}
