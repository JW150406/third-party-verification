<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToUserAssignedFormsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_assigned_forms', function(Blueprint $table)
		{
			$table->foreign('client_id')->references('id')->on('clients')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('form_id')->references('id')->on('clientsforms')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_assigned_forms', function(Blueprint $table)
		{
			$table->dropForeign('user_assigned_forms_client_id_foreign');
			$table->dropForeign('user_assigned_forms_form_id_foreign');
			$table->dropForeign('user_assigned_forms_user_id_foreign');
		});
	}

}
