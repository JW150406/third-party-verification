<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToTwilioStatisticsWorkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_statistics_workers', function (Blueprint $table) {
            $table->integer('cumulative_reservations_completed')->nullable();
            $table->integer('tasks_assigned')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_statistics_workers', function (Blueprint $table) {
            $table->dropColumn(['cumulative_reservations_completed','tasks_assigned']);
        });
    }
}
