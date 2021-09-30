<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWorkspaceNameInClientTwilioWorkspaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_twilio_workspace', function (Blueprint $table) {
            $table->string('workspace_name')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_twilio_workspace', function (Blueprint $table) {
            //
            $table->dropColumn('workspace_name');
        });
    }
}
