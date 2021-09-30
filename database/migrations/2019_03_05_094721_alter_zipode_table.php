<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterZipodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zip_codes', function (Blueprint $table) {
            //
            $table->string('city')->nullable();
            $table->dropColumn('name');
            $table->dropColumn('county_fips');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zip_codes', function (Blueprint $table) {
            //
            $table->dropColumn('city');
            $table->string('name')->nullable();
            $table->string('county_fips')->nullable();
        });
    }
}
