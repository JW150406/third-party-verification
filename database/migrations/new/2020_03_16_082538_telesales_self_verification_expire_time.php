<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TelesalesSelfVerificationExpireTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telesales_self_verify_exp_time', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('telesale_id')->nullable();
            $table->enum('verification_mode',['phone','email'])->nullable();
            $table->dateTime('expire_time')->nullable();
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
        Schema::dropIfExists('telesales_self_verify_exp_time');
    }
}
