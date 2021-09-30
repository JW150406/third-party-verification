<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWorkflowIdColmnInUserTwillioId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_twilio_id', function (Blueprint $table) {
            $table->string('workspace_id', 255)->nullable()->change();
            $table->string('workflow_id',255)->nullabe();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_twilio_id', function (Blueprint $table) {
            $table->dropColumn('workflow_id');
        });
    }
}
