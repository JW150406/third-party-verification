<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCodeColumnInSalescentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salescenters', function (Blueprint $table) {
           $table->string('code');
        });
        Schema::table('salescenters', function (Blueprint $table) {
             $table->unique(['client_id','code'],'unqiue_client_and_code');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salescenters', function (Blueprint $table) {
            $table->dropUnique('unqiue_client_and_code');
        });
        Schema::table('salescenters', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
}
