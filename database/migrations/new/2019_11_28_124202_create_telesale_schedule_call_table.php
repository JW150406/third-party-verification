<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTelesaleScheduleCallTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('telesale_schedule_call', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('telesale_id')->unsigned()->index('telesale_schedule_call_telesale_id_foreign');
			$table->enum('call_immediately', array('yes','no'));
			$table->dateTime('call_time')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('telesale_schedule_call');
	}

}
