<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwilioStatisticsWorkspaceActivityStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_statistics_workspace_activity_statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('workspace_id')->nullable();
            $table->string('realtime_friendly_name')->nullable();
            $table->bigInteger('realtime_sid')->nullable();
            $table->integer('realtime_workers')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twilio_statistics_workspace_activity_statistics');
    }
}
