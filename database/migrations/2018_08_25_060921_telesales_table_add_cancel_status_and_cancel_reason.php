<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TelesalesTableAddCancelStatusAndCancelReason extends Migration
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
            $table->enum('status', ['pending', 'verified','decline','hangup','cancel']);
            $table->longText('cancel_reason')->nullable();
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
            $table->dropColumn('cancel_reason'); 

        });
        Schema::table('telesales', function (Blueprint $table) {
            $table->enum('status', ['pending', 'verified','decline','hangup']);
        });
    }
}
