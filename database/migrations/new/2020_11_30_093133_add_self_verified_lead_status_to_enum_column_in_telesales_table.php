<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSelfVerifiedLeadStatusToEnumColumnInTelesalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE `telesales` CHANGE `status` `status` ENUM('pending','verified','decline','hangup','cancel','expired','self-verified') DEFAULT NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("ALTER TABLE `telesales` CHANGE `status` `status` ENUM('pending','verified','decline','hangup','cancel','expired') DEFAULT NULL;");
    }
}
