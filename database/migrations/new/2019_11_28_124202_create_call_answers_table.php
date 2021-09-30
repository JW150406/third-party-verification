<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCallAnswersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('call_answers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('lead_id');
			$table->integer('form_id');
			$table->integer('client_id');
			$table->integer('tpv_agent_id');
			$table->integer('sales_agent_id');
			$table->string('language')->nullable();
			$table->string('question')->nullable();
			$table->string('answer')->nullable();
			$table->string('verification_answer')->nullable();
			$table->string('custom_answer_checked')->nullable();
			$table->string('orignal_answer')->nullable();
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
		Schema::drop('call_answers');
	}

}
