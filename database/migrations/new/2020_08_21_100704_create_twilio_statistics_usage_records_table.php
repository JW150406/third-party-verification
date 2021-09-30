<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwilioStatisticsUsageRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twilio_statistics_usage_records', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('account_sid')->nullable();
            $table->string('page_size')->nullable();
            $table->string('category')->nullable();
            $table->string('count')->nullable();
            $table->string('price_unit')->nullable();
            $table->string('subresource_uris')->nullable();
            $table->string('description')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('as_of')->nullable();
            $table->string('usage_unit')->nullable();
            $table->string('price')->nullable();
            $table->string('usage')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->string('count_unit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twilio_statistics_usage_records');
    }
}
