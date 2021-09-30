<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCsvDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('csv_data', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('csv_filename');
			$table->boolean('csv_header')->default(0);
			$table->text('csv_data');
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
		Schema::drop('csv_data');
	}

}
