<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterVerificationMethodColumnCommentsInTelesalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE `telesales` CHANGE `verification_method` `verification_method` TINYINT(6) NULL DEFAULT NULL COMMENT '1 - Customer Inbound , 2 - Agent Inbound , 3 - Email , 4-Text, 5-IVR Inbound, 6-TPV Now Outbound';");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("ALTER TABLE `telesales` CHANGE `verification_method` `verification_method` TINYINT(5) NULL DEFAULT NULL COMMENT '1 - Customer Inbound , 2 - Agent Inbound , 3 - Email , 4-Text, 5-IVR Inbound';");
    }
}
