<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserTwilioIdTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_twilio_id', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->string('twilio_id');
			$table->string('workspace_id');
			$table->unique(['workspace_id','twilio_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_twilio_id');
	}

}
