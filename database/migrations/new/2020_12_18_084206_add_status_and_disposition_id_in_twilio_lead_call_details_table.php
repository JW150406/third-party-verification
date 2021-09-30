<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusAndDispositionIdInTwilioLeadCallDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_lead_call_details', function (Blueprint $table) {
            $table->string('lead_status')->after('call_type')->nullable();
            $table->integer('disposition_id')->after('lead_status')->nullable();
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
            $table->dropColumn(['lead_status','disposition_id']);
        });
    }
}
