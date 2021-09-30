<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClientsformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientsforms', function (Blueprint $table) {
            $table->dropColumn('form_fields');
            $table->dropColumn('utility_id');
            $table->text('description')->nullable()->after('client_id');
            $table->enum('channel', ['BOTH', 'WEB', 'MOBILE'])->default('BOTH')->after('description');
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
            $table->text('form_fields')->nullable()->after('client_id');
            $table->integer('utility_id')->nullable()->after('form_fields');
            $table->dropColumn('description');
            $table->dropColumn('channel');
        });
    }
}
