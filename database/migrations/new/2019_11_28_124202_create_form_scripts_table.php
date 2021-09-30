<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFormScriptsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('form_scripts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('client_id');
			$table->integer('form_id');
			$table->string('title');
			$table->integer('created_by');
			$table->string('language');
			$table->timestamps();
			$table->enum('scriptfor', array('salesagentintro','leadcreation','customer_verification','agent_not_found','closing','lead_not_found','after_lead_decline'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('form_scripts');
	}

}
