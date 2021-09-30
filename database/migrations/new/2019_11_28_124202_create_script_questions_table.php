<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScriptQuestionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('script_questions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('client_id');
			$table->integer('form_id');
			$table->integer('script_id');
			$table->integer('created_by');
			$table->text('question', 65535);
			$table->integer('position');
			$table->timestamps();
			$table->text('positive_ans', 65535)->nullable();
			$table->text('negative_ans', 65535)->nullable();
			$table->text('answer', 65535)->nullable();
			$table->boolean('is_customizable')->nullable();
			$table->string('state')->nullable();
			$table->string('commodity')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('script_questions');
	}

}
