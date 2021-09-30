<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableClientTwilioWorkflow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_twilio_workflowids', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->string('workspace_id');
            $table->string('workflow_id');
            $table->string('workflow_name');
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_twilio_workflowids');
    }
}
