<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClientsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clients', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->unique();
			$table->string('street');
			$table->string('city');
			$table->string('state');
			$table->string('country');
			$table->string('zip');
			$table->text('logo', 65535);
			$table->integer('created_by');
			$table->enum('status', array('active','inactive'));
			$table->timestamps();
			$table->string('code')->unique();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('clients');
	}

}
