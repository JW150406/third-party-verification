<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalescenterslocationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('salescenterslocations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('client_id');
			$table->string('name')->nullable();
			$table->string('street')->nullable();
			$table->string('city')->nullable();
			$table->string('state')->nullable();
			$table->string('country')->nullable();
			$table->string('zip')->nullable();
			$table->integer('created_by');
			$table->timestamps();
			$table->integer('salescenter_id')->default(0);
			$table->string('code');
			$table->unique(['client_id','salescenter_id','code'], 'unqiue_client_salescenter_and_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('salescenterslocations');
	}

}
