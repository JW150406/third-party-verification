<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLatLngColumnTypeInZipcodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zip_codes', function (Blueprint $table) {
            $table->decimal('lat',10,7)->default(0)->nullable()->change();
            $table->decimal('lng',10,7)->default(0)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('zip_codes', function (Blueprint $table) {
        //     $table->decimal('lat',10,7)->default(0);
        //     $table->decimal('lng',10,7)->default(0);
        // });
    }
}
