<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnMultienrollmentFieldInClientsformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientsforms', function (Blueprint $table) {
            $table->integer('multienrollment')->default(0);  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientsforms', function (Blueprint $table) {
            $table->dropColumn('multienrollment');
        });
    }
}
