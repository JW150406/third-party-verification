<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClientAgentNotFoundScriptsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('client_agent_not_found_scripts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('client_id')->unsigned()->index('client_agent_not_found_scripts_client_id_foreign');
			$table->integer('created_by')->unsigned()->index('client_agent_not_found_scripts_created_by_foreign');
			$table->text('question', 65535);
			$table->string('language');
			$table->integer('position');
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
		Schema::drop('client_agent_not_found_scripts');
	}

}
