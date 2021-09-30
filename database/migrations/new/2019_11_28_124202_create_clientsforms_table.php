<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClientsformsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clientsforms', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('client_id');
			$table->text('form_fields');
			$table->integer('created_by');
			$table->timestamps();
			$table->text('formname', 65535)->nullable();
			$table->integer('utility_id');
			$table->string('workspace_id');
			$table->string('workflow_id');
			$table->string('commodity_type')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('clientsforms');
	}

}
