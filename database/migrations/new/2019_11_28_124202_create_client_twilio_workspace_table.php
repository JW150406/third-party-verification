<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClientTwilioWorkspaceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('client_twilio_workspace', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('client_id');
			$table->string('workspace_id');
			$table->string('workspace_name')->nullable();
			$table->unique(['client_id','workspace_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('client_twilio_workspace');
	}

}
