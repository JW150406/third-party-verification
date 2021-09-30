<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePhonenumberverificationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('phonenumberverification', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('phonenumber');
			$table->string('otp')->nullable();
			$table->string('verifiedby')->nullable();
			$table->string('status')->nullable();
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
		Schema::drop('phonenumberverification');
	}

}
