<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedByAccessLevelInUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('parent_id')->default('0');
            $table->integer('client_id')->default('0');
            $table->integer('salescenter_id')->default('0');
            $table->enum('access_level', ['tpv', 'client','salescenter','salesagent']);
            $table->enum('status', ['active', 'inactive']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(array('parent_id', 'access_level','client_id','salescenter_id','status'));
        });
    }
}
