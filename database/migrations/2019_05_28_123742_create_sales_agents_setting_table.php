<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesAgentsSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salesagent_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id'); 
            $table->integer('passed_state_test')->default(0);  
            $table->string('state')->nullable(); 
            $table->integer('certified')->default(0);  
            $table->string('codeofconduct')->nullable(); 
            $table->integer('backgroundcheck')->default(0); 
            $table->integer('drugtest')->default(0); 
            $table->dateTime('certification_date')->nullable();  
            $table->unsignedInteger('added_by'); 
            $table->foreign('user_id')
              ->references('id')->on('users')
              ->onDelete('cascade'); 
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
        Schema::dropIfExists('salesagent_detail');
    }
}
