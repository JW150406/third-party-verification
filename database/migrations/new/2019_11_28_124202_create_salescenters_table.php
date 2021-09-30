<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalescentersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('salescenters', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->index();
			$table->string('street');
			$table->string('city');
			$table->string('state');
			$table->string('country');
			$table->string('zip');
			$table->integer('client_id')->index();
			$table->integer('created_by');
			$table->enum('status', array('active','inactive'));
			$table->timestamps();
			$table->string('code');
			$table->unique(['client_id','code'], 'unqiue_client_and_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('salescenters');
	}

}
