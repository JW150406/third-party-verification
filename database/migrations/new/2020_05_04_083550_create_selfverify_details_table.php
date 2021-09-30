<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSelfverifyDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selfverify_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('telesale_id');
            $table->string('ip')->nullable();
            $table->string('platform_name')->nullable();
            $table->string('plaform_model')->nullable();
            $table->string('os')->nullable();
            $table->string('os_version')->nullable();
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();
            $table->string('user_latitude')->nullable();
            $table->string('user_longitude')->nullable();
            $table->string('gps_location_image')->nullable();
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
        Schema::dropIfExists('selfverify_details');
    }
}
