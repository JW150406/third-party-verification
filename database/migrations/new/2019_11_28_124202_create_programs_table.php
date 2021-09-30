<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProgramsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('programs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->nullable();
			$table->string('code')->nullable();
			$table->string('rate')->nullable();
			$table->string('etf')->nullable();
			$table->string('msf')->nullable();
			$table->string('term')->nullable();
			$table->integer('client_id');
			$table->integer('created_by');
			$table->timestamps();
			$table->string('unit_of_measure')->nullable();
			$table->integer('utility_id');
			$table->string('dailyrate')->nullable();
			$table->string('producttype')->nullable();
			$table->string('termtype')->nullable();
			$table->string('customer_type')->nullable();
			$table->string('saleschannels')->nullable();
			$table->string('webbroker')->nullable();
			$table->string('product_filters')->nullable();
			$table->string('cis_system')->nullable();
			$table->string('isdefaulttieredeverGreen')->nullable();
			$table->string('istierpricing')->nullable();
			$table->string('isshell')->nullable();
			$table->string('current_selling_product')->nullable();
			$table->string('product_id')->nullable();
			$table->string('state')->nullable();
			$table->string('accountnumberlength')->nullable();
			$table->string('accountnumbertype')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('programs');
	}

}
