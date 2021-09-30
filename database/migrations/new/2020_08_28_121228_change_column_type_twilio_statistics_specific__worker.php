<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnTypeTwilioStatisticsSpecificWorker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_statistics_specific_Worker', function (Blueprint $table) {
            $table->string('Worker_id')->change();
            $table->string('Workspace_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_statistics_specific_Worker', function (Blueprint $table) {
            //
        });
    }
}
