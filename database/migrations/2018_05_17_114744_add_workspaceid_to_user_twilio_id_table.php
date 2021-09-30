<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWorkspaceidToUserTwilioIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_twilio_id', function (Blueprint $table) {            
            $table->dropUnique('user_twilio_id_twilio_id_unique');
            $table->string('workspace_id', 255);
            $table->unique(array('workspace_id', 'twilio_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_twilio_id', function (Blueprint $table) {
            //
            $table->dropUnique('user_twilio_id_workspace_id_twilio_id_unique');
            $table->dropColumn('workspace_id');            
            $table->unique('twilio_id');

        });
    }
}
