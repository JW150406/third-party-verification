<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeginKeyToTelesalesdataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telesalesdata', function (Blueprint $table) {
            //
            
            $table->dropColumn('telesale_id');

            

        });
        Schema::table('telesalesdata', function (Blueprint $table) {
            //
            
           

            $table->unsignedInteger('telesale_id');

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
        Schema::table('telesalesdata', function (Blueprint $table) {
            $table->integer('telesale_id');
        });
    }
}
