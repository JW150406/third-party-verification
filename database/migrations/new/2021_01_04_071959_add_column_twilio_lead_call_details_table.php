<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTwilioLeadCallDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_lead_call_details', function (Blueprint $table) {
	        $table->timestamp('task_assigned_time')->after('task_completed_time')->nullable();
	        $table->string('current_task_status')->after('task_completed_time')->nullable();
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
	        Schema::dropIfExists('twilio_lead_call_details');
        });
    }
}
