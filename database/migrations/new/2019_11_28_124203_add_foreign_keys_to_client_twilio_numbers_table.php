<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToClientTwilioNumbersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('client_twilio_numbers', function(Blueprint $table)
		{
			$table->foreign('client_id')->references('id')->on('clients')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('client_workflowid')->references('id')->on('client_twilio_workflowids')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('client_twilio_numbers', function(Blueprint $table)
		{
			$table->dropForeign('client_twilio_numbers_client_id_foreign');
			$table->dropForeign('client_twilio_numbers_client_workflowid_foreign');
		});
	}

}
