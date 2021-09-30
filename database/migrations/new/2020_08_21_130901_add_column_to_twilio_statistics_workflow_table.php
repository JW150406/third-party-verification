<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToTwilioStatisticsWorkflowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_statistics_workflow', function (Blueprint $table) {
            $table->integer('realtime_task_completed')->nullable()->after('realtime_task_reserved');
            $table->integer('cumulative_reservations_completed')->nullable()->after('cumulative_reservations_rejected');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_statistics_workflow', function (Blueprint $table) {
            //
        });
    }
}
