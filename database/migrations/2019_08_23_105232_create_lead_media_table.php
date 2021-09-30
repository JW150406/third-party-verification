<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leadmedia', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->string('size')->nullable();
            $table->longText('url')->nullable();
            $table->string('media_type')->nullable();
          
            $table->integer('telesales_id')->unsigned();
            $table->foreign('telesales_id')->references('id')->on('telesales')->onDelete('cascade');
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
        Schema::dropIfExists('leadmedia');
    }
}
