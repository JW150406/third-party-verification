<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateZipCodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('zip_codes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('zipcode')->nullable();
			$table->string('county')->nullable();
			$table->string('state')->nullable();
			$table->string('city')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('zip_codes');
	}

}
