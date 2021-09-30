<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTelesalesdataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('telesalesdata', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('meta_key');
			$table->string('meta_value')->nullable();
			$table->integer('telesale_id')->unsigned()->index('telesalesdata_telesale_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('telesalesdata');
	}

}
