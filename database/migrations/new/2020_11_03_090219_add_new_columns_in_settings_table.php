<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->integer('tpv_now_max_no_of_call_attempt')->after('is_enable_enroll_by_state')->nullable();
            $table->string('tpv_now_call_delay')->after('tpv_now_max_no_of_call_attempt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('tpv_now_max_no_of_call_attempt');
            $table->dropColumn('tpv_now_call_delay');
        });
    }
}
