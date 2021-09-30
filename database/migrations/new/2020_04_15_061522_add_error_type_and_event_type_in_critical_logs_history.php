<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddErrorTypeAndEventTypeInCriticalLogsHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('critical_logs_history', function (Blueprint $table) {
            $table->boolean("error_type")->default(0)->after('related_lead_ids')->comment("0 -  not-critical 1- critical");
            $table->integer("event_type")->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('critical_logs_history', function (Blueprint $table) {
            $table->dropColumns(['error_type',"event_type"]);
        });
    }
}
