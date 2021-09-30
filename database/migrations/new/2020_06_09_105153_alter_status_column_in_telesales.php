<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStatusColumnInTelesales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE telesales MODIFY COLUMN status ENUM('pending','verified','decline','hangup','cancel','expired')");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("ALTER TABLE telesales MODIFY COLUMN status ENUM('pending','verified','decline','hangup','cancel')");
    }
}
