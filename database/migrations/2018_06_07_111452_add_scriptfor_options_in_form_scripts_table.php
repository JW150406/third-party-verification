<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScriptforOptionsInFormScriptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_scripts', function (Blueprint $table) {
            $table->dropColumn('scriptfor'); 
       });
        Schema::table('form_scripts', function (Blueprint $table) {
            $table->enum('scriptfor', ['salesagentintro', 'leadcreation','customer_verification','agent_not_found','closing','lead_not_found']); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_scripts', function (Blueprint $table) {
            $table->dropColumn('scriptfor'); 
       });

        Schema::table('form_scripts', function (Blueprint $table) {
            $table->enum('scriptfor', ['salesagentintro', 'leadcreation','customer_verification']); 
        });
    }
}
