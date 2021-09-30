<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUtilityValidationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('utility_validations', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('client_id');
			$table->integer('utility_id')->unsigned()->index('utility_validations_utility_id_foreign');
			$table->string('label')->nullable();
			$table->string('regex')->nullable();
			$table->string('regex_message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('utility_validations');
    }
}
