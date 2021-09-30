<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUtilityZipcodeMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('utility_zipcodes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('utility_id');
            $table->unsignedInteger('zipcode_id');
            $table->foreign('utility_id')
              ->references('id')->on('utilities')
              ->onDelete('cascade');
              $table->foreign('zipcode_id')
                ->references('id')->on('zip_codes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('utility_zipcodes');
    }
}
