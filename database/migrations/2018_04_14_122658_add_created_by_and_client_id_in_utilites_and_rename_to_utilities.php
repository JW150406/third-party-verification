<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedByAndClientIdInUtilitesAndRenameToUtilities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('utilites', function (Blueprint $table) {
            $table->integer('client_id');
            $table->integer('created_by');
        });
        Schema::rename('utilites', 'utilities');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename( 'utilities', 'utilites');
        Schema::table('utilites', function (Blueprint $table) {
            $table->dropColumn(['client_id', 'created_by']);
        });
    }
}
