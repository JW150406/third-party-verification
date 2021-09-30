<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnTypeInTwilioStatisticsWorkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_statistics_workers', function (Blueprint $table) {
            $table->string('workspace_id')->change();
            $table->string('account_id')->change();
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
            //
        });
    }
}
