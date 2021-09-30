<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyInTwilioStatisticsWorkspaceActivityDuration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_statistics_workspace_activity_statistics', function (Blueprint $table) {
            $table->integer('workspaces_id')->unsigned()->nullable()->after('id');
            $table->index('workspaces_id','twilio_workspaces_workspaces_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_statistics_workspace_activity_statistics', function (Blueprint $table) {
            $table->dropIndex('twilio_workspaces_workspaces_id');
            $table->dropColumn('workspaces_id');
        });
    }
}
