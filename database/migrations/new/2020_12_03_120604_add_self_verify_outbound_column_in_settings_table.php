<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSelfVerifyOutboundColumnInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('is_enable_self_tpv_welcome_call')->default(1)->after('tpv_now_call_delay');
            $table->integer('self_tpv_max_no_of_call_attempt')->nullable()->after('is_enable_self_tpv_welcome_call');
            $table->string('self_tpv_call_delay')->nullable()->after('self_tpv_max_no_of_call_attempt');
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
            $table->dropColumn(['is_enable_self_tpv_welcome_call','self_tpv_max_no_of_call_attempt','self_tpv_call_delay']);
        });
    }
}
