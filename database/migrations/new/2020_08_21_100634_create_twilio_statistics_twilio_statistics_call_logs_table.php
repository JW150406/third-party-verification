<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwilioStatisticsTwilioStatisticsCallLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_statistics_call_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('account_sid')->nullable();
            $table->string('annotation')->nullable();
            $table->string('answered_by')->nullable();
            $table->string('caller_name')->nullable();
            $table->string('direction')->nullable();
            $table->integer('duration')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->string('forwarded_from')->nullable();
            $table->string('from')->nullable();
            $table->string('from_formatted')->nullable();
            $table->bigInteger('group_sid')->nullable();
            $table->string('parent_call_sid')->nullable();
            $table->string('phone_number_sid')->nullable();
            $table->string('price')->nullable();
            $table->string('price_unit')->nullable();
            $table->bigInteger('call_sid')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->string('status')->nullable();
            $table->string('subresource_uris_recordings')->nullable();
            $table->string('to')->nullable();
            $table->string('to_formatted')->nullable();
            $table->integer('queue_time')->nullable();
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
        Schema::dropIfExists('twilio_statistics_call_logs');
    }
}
