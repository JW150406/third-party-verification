<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTelesalesTmpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telesales_tmp', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->integer('form_id');
            $table->integer('user_id');
            $table->text('zipcode')->nullable();
            $table->string('refrence_id')->unique();
            $table->integer('reviewed_by')->nullable();
      			$table->integer('disposition_id')->nullable();
      			$table->integer('parent_id')->default(0);
      			$table->integer('cloned_by')->default(0);
      			$table->string('call_id')->nullable();
      			$table->string('twilio_recording_url')->nullable();
      			$table->string('s3_recording_url')->nullable();
      			$table->string('recording_id')->nullable();
      			$table->string('recording_downloaded', 10)->default('0');
      			$table->enum('status', array('pending','verified','decline','hangup','cancel'));
      			$table->text('cancel_reason')->nullable();
      			$table->string('verification_number')->nullable();
      			$table->string('call_duration')->nullable();
      			$table->string('is_multiple')->default('0');
      			$table->string('multiple_parent_id')->default('0');
      			$table->boolean('is_proceed')->default('0');
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
        Schema::dropIfExists('telesales_tmp');
    }
}
