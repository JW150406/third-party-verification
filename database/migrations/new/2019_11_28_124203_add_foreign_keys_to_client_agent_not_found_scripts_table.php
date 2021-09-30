<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToClientAgentNotFoundScriptsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('client_agent_not_found_scripts', function(Blueprint $table)
		{
			$table->foreign('client_id')->references('id')->on('clients')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('created_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('client_agent_not_found_scripts', function(Blueprint $table)
		{
			$table->dropForeign('client_agent_not_found_scripts_client_id_foreign');
			$table->dropForeign('client_agent_not_found_scripts_created_by_foreign');
		});
	}

}
