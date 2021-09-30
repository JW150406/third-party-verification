<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMultipleColumnsInTelesaleScheduleCallTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telesale_schedule_call', function (Blueprint $table) {
            $table->integer('attempt_no')->unsigned()->nullable();
            $table->string('disposition')->nullable();
            $table->enum('dial_status',['completed','answered','busy',' no-answer','failed','cancelled'])->nullable();
            $table->string('task_id')->nullable();
            $table->enum('schedule_status',['pending','task-created','cancelled','attempted'])->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telesale_schedule_call', function (Blueprint $table) {
            $table->dropColumn(['attempt_no', 'disposition', 'dial_status', 'task_id', 'schedule_status', 'created_at', 'updated_at', 'deleted_at']);
        });
    }
}
