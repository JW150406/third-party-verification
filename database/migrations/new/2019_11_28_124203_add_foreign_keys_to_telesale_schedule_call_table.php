<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTelesaleScheduleCallTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('telesale_schedule_call', function(Blueprint $table)
		{
			$table->foreign('telesale_id')->references('id')->on('telesales')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('telesale_schedule_call', function(Blueprint $table)
		{
			$table->dropForeign('telesale_schedule_call_telesale_id_foreign');
		});
	}

}
