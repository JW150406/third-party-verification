<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropForeignKeyConstraintFromCriticalLogsHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('critical_logs_history', function (Blueprint $table) {
            $table->dropForeign("critical_logs_history_lead_id_foreign");
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
            $table->foreign('lead_id')->references('id')->on('telesales')->onUpdate('RESTRICT')->onDelete('CASCADE');

        });
    }
}
