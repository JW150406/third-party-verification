<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNoAnswerEnumValueInTelesaleSchduleCallTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE `telesale_schedule_call` CHANGE `dial_status` `dial_status` ENUM('completed','answered','busy','no-answer','failed','cancelled');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("ALTER TABLE `telesale_schedule_call` CHANGE `dial_status` `dial_status` ENUM('completed','answered','busy',' no-answer','failed','cancelled');");
    }
}
