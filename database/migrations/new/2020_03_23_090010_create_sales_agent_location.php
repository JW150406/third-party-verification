<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesAgentLocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salesagentlocations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('salesagent_id')->unsigned();
            $table->foreign('salesagent_id')->references('id')->on('users');
            $table->decimal('lat',10,7);
            $table->decimal('lng',10,7);
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
        Schema::dropIfExists('salesagentlocations');
    }
}
