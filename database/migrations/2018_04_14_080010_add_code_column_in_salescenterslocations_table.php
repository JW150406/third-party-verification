<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCodeColumnInSalescenterslocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salescenterslocations', function (Blueprint $table) {
            $table->string('code');
        });
        Schema::table('salescenterslocations', function (Blueprint $table) {
            $table->unique(['client_id','salescenter_id','code'],'unqiue_client_salescenter_and_code');
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
            $table->dropUnique('unqiue_client_salescenter_and_code');
        });
        Schema::table('salescenterslocations', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
}
