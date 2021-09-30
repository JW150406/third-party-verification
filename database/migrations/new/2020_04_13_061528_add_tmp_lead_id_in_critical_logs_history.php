<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTmpLeadIdInCriticalLogsHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('critical_logs_history', function (Blueprint $table) {
            $table->integer('tmp_lead_id')->after('lead_id')->nullable();
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
            $table->dropColumn('tmp_lead_id');
        });
    }
}
