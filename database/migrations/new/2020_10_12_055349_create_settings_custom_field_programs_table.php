<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsCustomFieldProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings_custom_field_programs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->unsigned();
            $table->string('label_custom_field_1')->nullable();
            $table->boolean('is_enable_field_1')->default(0);
            $table->string('label_custom_field_2')->nullable();
            $table->boolean('is_enable_field_2')->default(0);
            $table->string('label_custom_field_3')->nullable();
            $table->boolean('is_enable_field_3')->default(0);
            $table->string('label_custom_field_4')->nullable();
            $table->boolean('is_enable_field_4')->default(0);
            $table->string('label_custom_field_5')->nullable();
            $table->boolean('is_enable_field_5')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings_custom_field_programs');
    }
}
