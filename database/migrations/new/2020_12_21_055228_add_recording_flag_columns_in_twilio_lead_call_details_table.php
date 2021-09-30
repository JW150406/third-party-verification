<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRecordingFlagColumnsInTwilioLeadCallDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_lead_call_details', function (Blueprint $table) {
            $table->boolean('recording_deleted_on_twilio')->default(0);
            $table->boolean('recording_downloaded')->default(0);
            $table->renameColumn('twilio_recording_url','twilio_recording_id');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_lead_call_details', function (Blueprint $table) {
            $table->dropColumn(['recording_deleted_on_twilio','recording_downloaded']);
        });
    }
}
