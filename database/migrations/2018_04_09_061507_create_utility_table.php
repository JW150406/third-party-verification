<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUtilityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('utilites', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company')->nullable();
            $table->string('state')->nullable();
            $table->string('commodity')->nullable();
            $table->string('namekey')->nullable();
            $table->string('utilityname')->nullable();
            $table->string('utilityshortname')->nullable();
            $table->string('enrollmentcriteria')->nullable();
            $table->integer('accountnumberlength');
            $table->mediumText('notes');
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
        Schema::dropIfExists('utilites');
    }
}
