<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('programs', function (Blueprint $table) {
           $table->dropForeign('programs_utility_id_foreign');

           $table->dropColumn('utility_id');
           $table->string('state')->nullable();
           $table->integer('accountnumberlength')->nullable();
           $table->boolean('accountnumber_fixed_length')->nullable();
           $table->boolean('meternumber')->nullable();
           $table->string('rescind_by')->nullable();
           $table->boolean('hefpa')->nullable();
           $table->string('unit_of_measure')->nullable();
           $table->string('utility_type')->nullable();
           $table->string('ldc_code')->nullable();
           $table->string('premise_type')->nullable();
           $table->string('account_number_type')->nullable();
           $table->string('brand')->nullable();
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
            $table->dropColumn([
              'state',
              'accountnumberlength',
              'accountnumber_fixed_length',
              'meternumber',
              'rescind_by',
              'hefpa',
              'unit_of_measure',
              'utility_type',
              'ldc_code',
              'premise_type',
              'account_number_type',
              'brand',
            ]);
        });
           $table->integer('utility_id');
    }
}
