<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCallIdRecordingIdAndRecordingUrlInTelesales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telesales', function (Blueprint $table) {
            $table->string('call_id')->nullable();
            $table->string('twilio_recording_url')->nullable();
            $table->string('s3_recording_url')->nullable();
            $table->string('recording_id')->nullable();
            $table->string('recording_downloaded',10)->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telesales', function (Blueprint $table) {
            $table->dropColumn(['call_id', 'twilio_recording_url','s3_recording_url', 'recording_id','recording_downloaded']);
        });
    }
}
