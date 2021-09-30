<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTwilioConnectedDevicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('twilio_connected_devices', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->text('device_id', 65535);
			$table->text('workers_online', 65535);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('twilio_connected_devices');
	}

}
