<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwilioWorkerReservationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_worker_reservation_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reservation_id')->nullable();
            $table->string('task_id')->nullable();
            $table->string('worker_id')->nullable();
            $table->timestamp('reservation_created_time')->nullable();
            $table->enum('reservation_status',['accepted','created','rejected','timeout'])->nullable();
            $table->enum('call_hung_up_by',['customer','agent'])->nullable();
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
        Schema::dropIfExists('twilio_worker_reservation_details');
    }
}
