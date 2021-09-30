<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTelesalesdataTmpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telesalesdata_tmp', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('field_id')->nullable();
          $table->string('meta_key');
          $table->string('meta_value')->nullable();
          $table->integer('telesaletmp_id')->unsigned()->index('telesalesdata_tmp_telesaletmp_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telesalesdata_tmp');
    }
}
