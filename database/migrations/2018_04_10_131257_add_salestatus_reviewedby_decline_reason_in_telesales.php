<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSalestatusReviewedbyDeclineReasonInTelesales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telesales', function (Blueprint $table) {
            $table->enum('status', ['pending', 'verified','decline']);
            $table->integer('reviewed_by')->nullable();
            $table->longText('decline_reason')->nullable();
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
            $table->dropColumn(['status', 'reviewed_by', 'decline_reason']);
        });
    }
}
