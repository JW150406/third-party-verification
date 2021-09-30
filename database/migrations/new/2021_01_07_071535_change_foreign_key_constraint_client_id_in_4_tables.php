<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeForeignKeyConstraintClientIdIn4Tables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_assigned_forms', function (Blueprint $table) {
            $table->dropForeign('user_assigned_forms_client_id_foreign');
            $table->foreign('client_id')->references('id')->on('clients')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::table('client_agent_not_found_scripts', function (Blueprint $table) {
            $table->dropForeign('client_agent_not_found_scripts_client_id_foreign');
            $table->foreign('client_id')->references('id')->on('clients')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::table('compliance_templates', function (Blueprint $table) {
            $table->dropForeign('compliance_templates_client_id_foreign');
            $table->foreign('client_id')->references('id')->on('clients')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::table('client_twilio_numbers', function (Blueprint $table) {
            $table->dropForeign('client_twilio_numbers_client_id_foreign');
            $table->foreign('client_id')->references('id')->on('clients')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_assigned_forms', function (Blueprint $table) {
            Schema::table('user_assigned_forms', function (Blueprint $table) {
                $table->dropForeign('user_assigned_forms_client_id_foreign');
                $table->foreign('client_id')->references('id')->on('clients')->onUpdate('RESTRICT')->onDelete('CASCADE');
            });
            Schema::table('client_agent_not_found_scripts', function (Blueprint $table) {
                $table->dropForeign('client_agent_not_found_scripts_client_id_foreign');
                $table->foreign('client_id')->references('id')->on('clients')->onUpdate('RESTRICT')->onDelete('CASCADE');
            });
            Schema::table('compliance_templates', function (Blueprint $table) {
                $table->dropForeign('compliance_templates_client_id_foreign');
                $table->foreign('client_id')->references('id')->on('clients')->onUpdate('RESTRICT')->onDelete('CASCADE');
            });
            Schema::table('client_twilio_numbers', function (Blueprint $table) {
                $table->dropForeign('client_twilio_numbers_client_id_foreign');
                $table->foreign('client_id')->references('id')->on('clients')->onUpdate('RESTRICT')->onDelete('CASCADE');
            });
        });
    }
}
