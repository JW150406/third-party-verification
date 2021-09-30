<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('first_name');
			$table->string('last_name')->nullable();
			$table->string('email')->unique();
			$table->string('userid')->unique();
			$table->string('password');
			$table->string('title')->nullable();
			$table->string('verification_code')->nullable();
			$table->string('remember_token', 100)->nullable();
			$table->text('session_id', 65535)->nullable()->comment('Stores the id of the user session');
			$table->timestamps();
			$table->integer('parent_id')->default(0)->index();
			$table->integer('client_id')->default(0)->index();
			$table->integer('salescenter_id')->default(0)->index();
			$table->enum('access_level', array('tpv','tpvagent','client','salescenter','salesagent'))->nullable();
			$table->enum('status', array('active','inactive'));
			$table->integer('location_id')->default(0);
			$table->dateTime('last_activity')->nullable();
			$table->text('deactivationreason')->nullable();
			$table->string('hire_options')->nullable();
			$table->text('profile_picture', 65535)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
