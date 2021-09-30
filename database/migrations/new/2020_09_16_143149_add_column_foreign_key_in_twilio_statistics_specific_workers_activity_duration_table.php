<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnForeignKeyInTwilioStatisticsSpecificWorkersActivityDurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_statistics_specific_Worker_activity_duration', function (Blueprint $table) {
            $table->integer('workers_id')->unsigned()->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_statistics_specific_Worker_activity_duration', function (Blueprint $table) {
            $table->dropColumn('workers_id');
        });
    }
}
