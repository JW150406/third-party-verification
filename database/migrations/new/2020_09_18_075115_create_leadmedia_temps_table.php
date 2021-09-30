<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadmediaTempsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leadmedia_temps', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('telesales_tmp_id')->index();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->string('url')->nullable();
            $table->date('expire')->nullable();
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
        Schema::dropIfExists('leadmedia_temps');
    }
}
