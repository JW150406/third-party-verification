<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsEnrollmentByStateNewColumnInTelesalesTmpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telesales_tmp', function (Blueprint $table) {
            $table->boolean('is_enrollment_by_state')->after('is_proceed')->default(0);
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
            $table->dropColumn(['is_enrollment_by_state']);
        });
    }
}
