<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStateEnrollmentCoumnsInSettingsCustomFieldProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings_custom_field_programs', function (Blueprint $table) {
            $table->boolean('is_enable_enroll_by_state')->default(0)->after('is_enable_field_5');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings_custom_field_programs', function (Blueprint $table) {
            $table->dropColumn(['is_enable_enroll_by_state']);
        });
    }
}
