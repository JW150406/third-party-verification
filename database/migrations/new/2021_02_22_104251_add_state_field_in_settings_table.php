<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStateFieldInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('restrict_states_self_tpv_tele')->after('is_enable_self_tpv_tele')->nullable();
            $table->text('restrict_states_self_tpv_d2d')->after('is_enable_self_tpv_d2d')->nullable();
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
            $table->dropColumn(['restrict_states_self_tpv_tele','restrict_states_self_tpv_d2d']);
        });
    }
}
