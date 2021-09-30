<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSalesagentLatLngColumnsInTelesalesTmpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telesales_tmp', function (Blueprint $table) {
            $table->string('salesagent_lat')->nullable()->after('is_proceed');
            $table->string('salesagent_lng')->nullable()->after('salesagent_lat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telesales_tmp', function (Blueprint $table) {
            $table->dropColumn('salesagent_lat');
            $table->dropColumn('salesagent_lng');
        });
    }
}
