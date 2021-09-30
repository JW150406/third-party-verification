<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateComplianceTemplatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('compliance_templates', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->integer('client_id')->unsigned()->index('compliance_templates_client_id_foreign');
			$table->integer('form_id')->unsigned()->index('compliance_templates_form_id_foreign');
			$table->integer('created_by')->unsigned()->index('compliance_templates_created_by_foreign');
			$table->text('fields')->comment('serialized data for fields');
			$table->timestamps();
			$table->integer('utility_id')->unsigned()->index('compliance_templates_utility_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('compliance_templates');
	}

}
