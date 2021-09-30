<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToComplianceTemplatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('compliance_templates', function(Blueprint $table)
		{
			$table->foreign('client_id')->references('id')->on('clients')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('created_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('form_id')->references('id')->on('clientsforms')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('utility_id')->references('id')->on('utilities')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('compliance_templates', function(Blueprint $table)
		{
			$table->dropForeign('compliance_templates_client_id_foreign');
			$table->dropForeign('compliance_templates_created_by_foreign');
			$table->dropForeign('compliance_templates_form_id_foreign');
			$table->dropForeign('compliance_templates_utility_id_foreign');
		});
	}

}
