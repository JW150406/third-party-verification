<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UtilityChangeTableStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('utilities', function (Blueprint $table) {
            //
            $table->dropColumn('state');
            $table->dropColumn('namekey');
            $table->dropColumn('utilityshortname');
            $table->dropColumn('enrollmentcriteria');
            $table->dropColumn('accountnumberlength');
            $table->dropColumn('notes');
            $table->dropColumn('phone');
            $table->dropColumn('email');
            $table->dropColumn('website_link');
            $table->dropColumn('utility_id');
            $table->dropColumn('gas_ldc_type');
            $table->dropColumn('elec_non_iou_type');
            $table->dropColumn('percent_of_overlap');
            $table->dropColumn('utility_territory_type');
            $table->dropColumn('oasis');
            $table->dropColumn('provider');
            $table->dropColumn('major');
            $table->dropColumn('spark');
            $table->dropColumn('ng_and_e');
            $table->dropColumn('spark_presence');
            $table->dropColumn('co_phone');
            $table->dropColumn('co_email');
            $table->dropColumn('co_website_link');
            $table->dropColumn('co_alt_name');
            $table->dropColumn('ptc_resi_rate');
            $table->dropColumn('ptc_small_rate');
            $table->dropColumn('comparison_site_resi');
            $table->dropColumn('comparison_resi_additional_info');
            $table->dropColumn('comparison_site_small');
            $table->dropColumn('comparison_small_additional_info');
            $table->dropColumn('additional_info');
            $table->dropColumn('deregulated');
            $table->dropColumn('service_area_type');
            $table->dropColumn('bill_name');
            $table->dropColumn('total_pop');
            $table->dropColumn('total_w_tec_score');
            $table->dropColumn('alt_name');
            


            /* new column */
            $table->string('market')->nullable();
            
         

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('utilities', function (Blueprint $table) {
            
            $table->string('state')->nullable(); 
            $table->string('namekey')->nullable();
            $table->string('utilityshortname')->nullable();
            $table->string('enrollmentcriteria')->nullable();
            $table->integer('accountnumberlength');
            $table->mediumText('notes');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website_link')->nullable();
            $table->string('alt_name')->nullable();
            $table->string('utility_id')->nullable();
            $table->string('gas_ldc_type')->nullable();
            $table->string('elec_non_iou_type')->nullable();
            $table->string('percent_of_overlap')->nullable();
            $table->string('utility_territory_type')->nullable();
            $table->string('oasis')->nullable();
            $table->string('provider')->nullable();
            $table->string('major')->nullable();
            $table->string('spark')->nullable();
            $table->string('ng_and_e')->nullable();
            $table->string('spark_presence')->nullable();
            $table->string('co_phone')->nullable();
            $table->string('co_email')->nullable();
            $table->string('co_website_link')->nullable();
            $table->string('co_alt_name')->nullable();
            $table->string('ptc_resi_rate')->nullable();
            $table->string('ptc_small_rate')->nullable();
            $table->string('comparison_site_resi')->nullable();
            $table->string('comparison_resi_additional_info')->nullable();
            $table->string('comparison_site_small')->nullable();
            $table->string('comparison_small_additional_info')->nullable();
            $table->string('additional_info')->nullable();
            $table->string('deregulated')->nullable();
            $table->string('service_area_type')->nullable();
            $table->string('bill_name')->nullable();
            $table->string('total_pop')->nullable();
            $table->string('total_w_tec_score')->nullable();


            $table->dropColumn('market'); 
            
        });
    }
}
