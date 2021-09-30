<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClientWorkflowidColumnInClientTwilioNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement('ALTER TABLE `client_twilio_numbers` CHANGE `client_workflowid` `client_workflowid` INT(10) UNSIGNED NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::statement('ALTER TABLE `client_twilio_numbers` CHANGE `client_workflowid` `client_workflowid` INT(10) UNSIGNED NOT NULL;');
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
