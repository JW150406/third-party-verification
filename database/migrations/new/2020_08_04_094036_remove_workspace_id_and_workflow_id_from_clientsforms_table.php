<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveWorkspaceIdAndWorkflowIdFromClientsformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clientsforms', function (Blueprint $table) {
            $table->dropColumn(['workspace_id', 'workflow_id']);
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
            $table->string('workspace_id', 255)->after('formname')->nullable();
            $table->string('workflow_id', 255)->after('workspace_id')->nullable();
        });
    }
}
