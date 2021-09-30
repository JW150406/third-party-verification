<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableClientTwilioNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_twilio_numbers', function (Blueprint $table) {
            // $table->enum('type',['customer_verification','customer_call_in_verification','ivr_tpv_verification'])->nullabe()->change();
            DB::statement("ALTER TABLE client_twilio_numbers MODIFY COLUMN type ENUM('customer_verification','customer_call_in_verification','ivr_tpv_verification')");

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
            DB::statement("ALTER TABLE client_twilio_numbers MODIFY COLUMN type ENUM('customer_verification','customer_call_in_verification')");
        });
    }
}
