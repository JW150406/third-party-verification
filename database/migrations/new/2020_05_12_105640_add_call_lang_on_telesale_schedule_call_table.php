<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCallLangOnTelesaleScheduleCallTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telesale_schedule_call', function (Blueprint $table) {
            $table->enum('call_lang',['en','es']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telesale_schedule_call', function (Blueprint $table) {
            $table->dropColumn('call_lang');
        });
    }
}
