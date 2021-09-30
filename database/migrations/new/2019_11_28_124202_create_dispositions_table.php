<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDispositionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dispositions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('type')->nullable();
			$table->text('description', 65535)->nullable();
			$table->integer('created_by');
			$table->timestamps();
			$table->string('allow_cloning', 20)->default('false');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('dispositions');
	}

}
