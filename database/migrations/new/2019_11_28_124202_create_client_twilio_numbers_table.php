<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClientTwilioNumbersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('client_twilio_numbers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('phonenumber');
			$table->integer('client_workflowid')->unsigned()->index('client_twilio_numbers_client_workflowid_foreign');
			$table->integer('client_id')->unsigned()->index('client_twilio_numbers_client_id_foreign');
			$table->integer('added_by');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('client_twilio_numbers');
	}

}
