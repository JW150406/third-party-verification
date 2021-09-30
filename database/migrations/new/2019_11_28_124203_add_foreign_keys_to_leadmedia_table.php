<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToLeadmediaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('leadmedia', function(Blueprint $table)
		{
			$table->foreign('telesales_id')->references('id')->on('telesales')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('leadmedia', function(Blueprint $table)
		{
			$table->dropForeign('leadmedia_telesales_id_foreign');
		});
	}

}
