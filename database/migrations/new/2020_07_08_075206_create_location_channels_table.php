<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_channels', function (Blueprint $table) {
            //$table->increments('id');
            $table->unsignedInteger('location_id')->index();
            $table->enum('channel', ['tele', 'd2d', 'retail'])->default('tele');
            $table->timestamps();
            $table->foreign('location_id')
                ->references('id')->on('salescenterslocations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_channels');
    }
}
