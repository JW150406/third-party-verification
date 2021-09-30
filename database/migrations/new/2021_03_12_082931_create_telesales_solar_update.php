<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTelesalesSolarUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telesales_solar_update', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('telesales_id');
            $table->string('assigned_kw')->nullable();
            $table->dateTime('assigned_date')->nullable();
            $table->string('updated_from_status')->nullable();
            $table->string('updated_to_status')->nullable();
            $table->integer('update_by_id');
            $table->string('update_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telesales_solar_update');
    }
}
