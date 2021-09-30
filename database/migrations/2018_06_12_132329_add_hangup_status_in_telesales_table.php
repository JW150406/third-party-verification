<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHangupStatusInTelesalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telesales', function (Blueprint $table) {
            $table->dropColumn('status'); 
        });
        Schema::table('telesales', function (Blueprint $table) {
            $table->enum('status', ['pending', 'verified','decline','hangup']);
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telesales', function (Blueprint $table) {
            $table->dropColumn('status'); 
        });
        Schema::table('telesales', function (Blueprint $table) {
            $table->enum('status', ['pending', 'verified','decline']);
        });
    }
}
