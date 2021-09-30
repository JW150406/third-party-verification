<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSalescenterIdColumnInSalescenterLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salescenterslocations', function (Blueprint $table) {
            $table->integer('salescenter_id')->default('0');
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
            $table->dropColumn('salescenter_id');
        });
    }
}
