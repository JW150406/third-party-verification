<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnTypeTwilioStatisticsTaskquue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_statistics_taskqueue', function (Blueprint $table) {
            $table->string('workspace_id')->change();
            $table->string('account_sid')->change();
            $table->string('task_queue_sid')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_statistics_taskqueue', function (Blueprint $table) {
            //
        });
    }
}
