<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCriticalLogsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('critical_logs_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lead_id')->unsigned()->nullable();
            $table->foreign('lead_id')->references('id')->on('telesales')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->string('user_type')->nullable()->nullable()->comment("1: Customer , 2:System");
            $table->integer('sales_agent_id')->unsigned()->nullable();
            $table->foreign('sales_agent_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->integer('tpv_agent_id')->unsigned()->nullable();
            $table->foreign('tpv_agent_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->string('reason')->nullable();
            $table->string('related_lead_ids')->nullable();
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
        Schema::dropIfExists('critical_logs_history');
    }
}
