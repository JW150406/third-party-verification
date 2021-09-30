<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableForconnectedDevicesToTwilio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_connected_devices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->text('device_id');            
            $table->text('workers_online');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twilio_connected_devices');
    }
}
