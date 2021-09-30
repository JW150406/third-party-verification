<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFraudAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fraud_alerts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->nullable()->comment('Email');
            $table->string('phone')->nullable()->comment('Phone Number');
            $table->enum('alert_level',['client','salescenter','sclocation'])->nullable()->comment('Alert Level');
            $table->integer('client_id')->nullable()->comment('Client Id');
            $table->string('salescenter_id')->nullable()->comment('Salescenter Id');
            $table->string('location_id')->nullable()->comment('Location Id');
            $table->integer('added_by')->nullable();
            $table->integer('added_for_client')->nullable();
            $table->enum('type',['email','phone'])->nullable();
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
        Schema::dropIfExists('fraud_alerts');
    }
}
