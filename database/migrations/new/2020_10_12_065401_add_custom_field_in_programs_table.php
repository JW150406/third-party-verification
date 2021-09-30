<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomFieldInProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->string('custom_field_1',255)->nullable();
            $table->string('custom_field_2',255)->nullable();
            $table->string('custom_field_3',255)->nullable();
            $table->string('custom_field_4',255)->nullable();
            $table->string('custom_field_5',255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['custom_field_1','custom_field_2','custom_field_3','custom_field_4','custom_field_5']);
        });
    }
}
