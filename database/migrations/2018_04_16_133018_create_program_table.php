<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgramTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('rate')->nullable();
            $table->string('etf')->nullable();
            $table->string('msf')->nullable();
            $table->string('term')->nullable();
            $table->unsignedInteger('utility_id');
            $table->integer('client_id');
            $table->integer('created_by');
            $table->timestamps();
            $table->foreign('utility_id')->references('id')->on('utilities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('programs');
    }
}
