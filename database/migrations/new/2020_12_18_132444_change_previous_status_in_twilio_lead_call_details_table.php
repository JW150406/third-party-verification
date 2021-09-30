<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePreviousStatusInTwilioLeadCallDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_lead_call_details', function (Blueprint $table) {
            $table->string('previous_status')->nullable()->change();
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
            $table->integer('previous_status')->nullable();
        });
    }
}
