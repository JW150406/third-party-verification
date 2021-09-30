<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalesagentDetailTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('salesagent_detail', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index('salesagent_detail_user_id_foreign');
			$table->integer('passed_state_test')->default(0);
			$table->string('state')->nullable();
			$table->integer('certified')->default(0);
			$table->string('codeofconduct')->nullable();
			$table->integer('backgroundcheck')->default(0);
			$table->integer('drugtest')->default(0);
			$table->dateTime('certification_date')->nullable();
			$table->integer('added_by')->unsigned();
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
		Schema::drop('salesagent_detail');
	}

}
