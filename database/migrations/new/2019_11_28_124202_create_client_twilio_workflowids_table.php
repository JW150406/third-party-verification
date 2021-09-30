<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClientTwilioWorkflowidsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('client_twilio_workflowids', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('client_id');
			$table->string('workspace_id');
			$table->string('workflow_id');
			$table->string('workflow_name');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('client_twilio_workflowids');
	}

}
