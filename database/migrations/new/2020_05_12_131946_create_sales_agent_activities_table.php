<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesAgentActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_agent_activities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('agent_id');
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->integer('total_time')->comment('In seconds')->default(0);
            $table->enum('activity_type',['clock_in','clock_out','break_in','break_out','arrival_in','arrival_out'])->default('clock_in');
            $table->decimal('start_lat',10,7)->nullable();
            $table->decimal('start_lng',10,7)->nullable();
            $table->decimal('end_lat',10,7)->nullable();
            $table->decimal('end_lng',10,7)->nullable();
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
        Schema::dropIfExists('sales_agent_activities');
    }
}
