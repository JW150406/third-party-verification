<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwilioNumbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_twilio_numbers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phonenumber');
            $table->unsignedInteger('client_workflowid');
            $table->unsignedInteger('client_id');
            $table->integer('added_by');

            $table->foreign('client_workflowid')
            ->references('id')->on('client_twilio_workflowids')
            ->onDelete('cascade');
            $table->foreign('client_id')
            ->references('id')->on('clients')
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
        Schema::dropIfExists('client_twilio_numbers');
    }
}
