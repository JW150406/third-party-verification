<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUtilityIdColumnInComplianceTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('compliance_templates', function (Blueprint $table) {
            $table->unsignedInteger('utility_id');
            $table->foreign('utility_id')
                ->references('id')->on('utilities')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('compliance_templates', function (Blueprint $table) {
            $table->dropColumn('utility_id');
        });
    }
}
