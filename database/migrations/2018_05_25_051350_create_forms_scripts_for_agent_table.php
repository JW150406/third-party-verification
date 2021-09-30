<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormsScriptsForAgentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_scripts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->integer('form_id');
            $table->string('title');
            $table->integer('created_by');
            $table->string('language');
            $table->enum('scriptfor', ['customer', 'salesagent']);            
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
        Schema::dropIfExists('form_scripts');
    }
}
