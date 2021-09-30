<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTpvNowRestrictedTimezonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings_tpv_now_restricted_timezones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->string('state');
            $table->string('start_time');
            $table->string('end_time');
            $table->string('timezone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings_tpv_now_restricted_timezones');
    }
}
