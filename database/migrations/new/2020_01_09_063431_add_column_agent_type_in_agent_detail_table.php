<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class AddColumnAgentTypeInAgentDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salesagent_detail', function (Blueprint $table) {
            $table->enum('agent_type',['tele','d2d'])->nullable();
            $table->dateTime('certification_exp_date')->nullable();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salesagent_detail', function (Blueprint $table) {
            $table->dropColumn(['agent_type','certification_exp_date']);
        });
    }
}