<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTelesaleScheduleCallTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telesale_schedule_call', function (Blueprint $table) {
            $table->increments('id'); 
            $table->unsignedInteger('telesale_id');
            $table->enum('call_immediately', ['yes', 'no']);
            $table->dateTime('call_time')->nullable();
            $table->foreign('telesale_id')
                    ->references('id')->on('telesales')
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
        Schema::dropIfExists('telesale_schedule_call');
    }
}
