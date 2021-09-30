<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOutboundDisconnectColumnInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('is_outbound_disconnect')->default(0)->after('lead_expiry_time');
            $table->integer('outbound_disconnect_max_reschedule_call_attempt')->nullable()->after('is_outbound_disconnect');
            $table->string('outbound_disconnect_schedule_call_delay')->nullable()->after('outbound_disconnect_max_reschedule_call_attempt');
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
            $table->dropColumn(['is_outbound_disconnect', 'outbound_disconnect_max_reschedule_call_attempt', 'outbound_disconnect_schedule_call_delay']);
        });
    }
}
