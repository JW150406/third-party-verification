<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnTypeTwilioStatisticsTwilioStatisticsCallLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_statistics_call_logs', function (Blueprint $table) {
            $table->string('group_sid')->change();
            $table->string('account_sid')->change();
            $table->string('parent_call_sid')->change();
            $table->string('phone_number_sid')->change();
            $table->string('call_sid')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_statistics_call_logs', function (Blueprint $table) {
            //
        });
    }
}
