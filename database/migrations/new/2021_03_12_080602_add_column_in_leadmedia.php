<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnInLeadmedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leadmedia', function (Blueprint $table) {
            $table->string('ip_address')->nullable();
        });
        Schema::table('leadmedia_temps', function (Blueprint $table) {
            $table->string('ip_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leadmedia', function (Blueprint $table) {
            $table->dropColumn('ip_address');
        });
        Schema::table('leadmedia_temps', function (Blueprint $table) {
            $table->dropColumn('ip_address');
        });
    }
}
