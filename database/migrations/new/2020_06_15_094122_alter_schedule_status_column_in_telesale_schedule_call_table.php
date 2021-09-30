<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterScheduleStatusColumnInTelesaleScheduleCallTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE `telesale_schedule_call` CHANGE `schedule_status` `schedule_status` ENUM('pending','task-created','cancelled','attempted','skip')  DEFAULT 'pending';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("ALTER TABLE `telesale_schedule_call` CHANGE `schedule_status` `schedule_status` ENUM('pending','task-created','cancelled','attempted')  DEFAULT 'pending';");
    }
}
