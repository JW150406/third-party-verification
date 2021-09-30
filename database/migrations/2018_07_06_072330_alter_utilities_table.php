<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUtilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('utilities', function (Blueprint $table) {
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
             $table->dropColumn([
               'utility_id',
               'gas_ldc_type',
               'elec_non_iou_type',
               'percent_of_overlap',
               'utility_territory_type',
               'oasis',
               'provider',
               'major',
               'spark',
               'ng_and_e',
               'spark_presence',
               'co_phone',
               'co_email',
               'co_website_link',
               'co_alt_name',
               'phone',
               'email',
               'website_link',
               'alt_name',
               'ptc_resi_rate',
               'ptc_small_rate',
               'comparison_site_small',
               'additional_info',
               'deregulated',
               'service_area_type',
               'bill_name',
               'total_pop',
               'total_w_tec_score',
               'comparison_small_additional_info',
               'comparison_site_resi',
               'comparison_resi_additional_info',
             ]);
        });
    }
}
