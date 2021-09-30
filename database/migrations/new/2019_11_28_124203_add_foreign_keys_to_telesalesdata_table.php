<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTelesalesdataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('telesalesdata', function(Blueprint $table)
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
		Schema::table('telesalesdata', function(Blueprint $table)
		{
			$table->dropForeign('telesalesdata_telesale_id_foreign');
		});
	}

}
