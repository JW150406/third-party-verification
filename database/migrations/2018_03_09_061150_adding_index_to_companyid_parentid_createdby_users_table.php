<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddingIndexToCompanyidParentidCreatedbyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
           $table->index('parent_id');
           $table->index('client_id');
           $table->index('salescenter_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['client_id']);
            $table->dropIndex(['salescenter_id']);
        });
    }
}
