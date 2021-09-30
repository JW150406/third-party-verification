<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPrograms_Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->integer('utility_id');
            $table->string('dailyrate')->nullable();
            $table->string('producttype')->nullable();
            $table->string('termtype')->nullable();
            $table->string('customer_type')->nullable();
            $table->string('saleschannels')->nullable();
            $table->string('webbroker')->nullable();
            $table->string('product_filters')->nullable();
            $table->string('cis_system')->nullable();
            $table->string('isdefaulttieredeverGreen')->nullable();
            $table->string('istierpricing')->nullable();
            $table->string('isshell')->nullable();
            $table->string('current_selling_product')->nullable();
            $table->string('product_idh')->nullable();
            $table->dropColumn([
                'state',
                'accountnumberlength',
                'accountnumber_fixed_length',
                'meternumber',
                'rescind_by',
                'hefpa', 
                'utility_type',
                'ldc_code',
                'premise_type',
                'account_number_type',
                'brand',
              ]);

            //
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
           $table->string('state')->nullable();
           $table->integer('accountnumberlength')->nullable();
           $table->boolean('accountnumber_fixed_length')->nullable();
           $table->boolean('meternumber')->nullable();
           $table->string('rescind_by')->nullable();
           $table->boolean('hefpa')->nullable(); 
           $table->string('utility_type')->nullable();
           $table->string('ldc_code')->nullable();
           $table->string('premise_type')->nullable();
           $table->string('account_number_type')->nullable();
           $table->string('brand')->nullable();
            //
 
            $table->dropColumn([
                'utility_id',
                'dailyrate',
                'producttype',
                'termtype',
                'customer_type',
                'saleschannels',
                'webbroker',
                'product_filters',
                'cis_system',
                'isdefaulttieredeverGreen',
                'istierpricing',
                'isshell',
                'current_selling_product',
                'product_idh'
              ]);
        });
    }
}
