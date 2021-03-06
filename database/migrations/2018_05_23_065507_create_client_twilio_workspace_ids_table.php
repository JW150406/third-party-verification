<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientTwilioWorkspaceIdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_twilio_workspace', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->string('workspace_id', 255);
            $table->unique(array('client_id', 'workspace_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_twilio_workspace');
    }
}
