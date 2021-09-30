<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToUtilityZipcodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('utility_zipcodes', function(Blueprint $table)
		{
			$table->foreign('utility_id')->references('id')->on('utilities')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('zipcode_id')->references('id')->on('zip_codes')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('utility_zipcodes', function(Blueprint $table)
		{
			$table->dropForeign('utility_zipcodes_utility_id_foreign');
			$table->dropForeign('utility_zipcodes_zipcode_id_foreign');
		});
	}

}
