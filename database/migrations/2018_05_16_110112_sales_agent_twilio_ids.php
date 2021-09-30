<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SalesAgentTwilioIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_twilio_id', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('twilio_id')
                  ->unique();
          
        });         
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_twilio_id');
    }
}
