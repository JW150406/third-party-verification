<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUtilityZipcodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('utility_zipcodes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('utility_id')->unsigned()->index('utility_zipcodes_utility_id_foreign');
			$table->integer('zipcode_id')->unsigned()->index('utility_zipcodes_zipcode_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('utility_zipcodes');
	}

}
