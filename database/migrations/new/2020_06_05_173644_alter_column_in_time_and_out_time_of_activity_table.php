<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnInTimeAndOutTimeOfActivityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_agent_activities', function (Blueprint $table) {
            $table->dropColumn(['in_time','out_time']);
        });

        Schema::table('sales_agent_activities', function (Blueprint $table) {
            $table->timestamp('in_time')->nullable()->after('agent_id');
            $table->timestamp('out_time')->nullable()->after('in_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_agent_activities', function (Blueprint $table) {
            //
        });
    }
}
