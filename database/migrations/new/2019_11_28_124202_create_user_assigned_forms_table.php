<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserAssignedFormsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_assigned_forms', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('client_id')->unsigned()->index('user_assigned_forms_client_id_foreign');
			$table->integer('user_id')->unsigned()->index('user_assigned_forms_user_id_foreign');
			$table->integer('form_id')->unsigned()->index('user_assigned_forms_form_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_assigned_forms');
	}

}
