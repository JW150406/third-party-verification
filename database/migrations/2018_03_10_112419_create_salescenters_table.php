<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalescentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salescenters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('street');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->string('zip');
            $table->integer('client_id');
            $table->integer('created_by');
            $table->enum('status', ['active', 'inactive']);
            $table->timestamps();
        });
        DB::update("ALTER TABLE salescenters AUTO_INCREMENT = 2000;");
        Schema::table('salescenters', function (Blueprint $table) {
            $table->index('name');
            $table->index('client_id');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salescenters');
    }
}
