<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnContactNameAndNumberSalesLoc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salescenterslocations', function (Blueprint $table) {
            $table->string('contact_name', 255)->nullable();
            $table->string('contact_number', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salescenterslocations', function (Blueprint $table) {
            $table->dropColumn(['contact_name','contact_number']);
        });
    }
}
