<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPreviousStatusInTwilioLeadCallDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_lead_call_details', function (Blueprint $table) {
            $table->integer('previous_status')->before('lead_status')->nullable();
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
            $table->dropColumn('previous_status');
        });
    }
}
