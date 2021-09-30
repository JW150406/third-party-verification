<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUtilitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('utilities', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('company')->nullable();
			$table->string('commodity')->nullable();
			$table->string('utilityname')->nullable();
			$table->timestamps();
			$table->integer('client_id');
			$table->integer('created_by');
			$table->string('market')->nullable();
			$table->string('commodity_type')->nullable();
			$table->string('fullname')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('utilities');
	}

}
