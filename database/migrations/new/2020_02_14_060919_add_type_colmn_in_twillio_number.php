<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeColmnInTwillioNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_twilio_numbers', function (Blueprint $table) {
            $table->enum('type',['customer_verification','customer_call_in_verification'])->nullabe();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_twilio_numbers', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
