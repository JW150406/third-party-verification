<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConditionTypeColumnInScriptQuestionsConditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('script_questions_conditions', function (Blueprint $table) {
            $table->enum('condition_type',['tag','question'])->default('tag')->after('comparison_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('script_questions_conditions', function (Blueprint $table) {
            $table->dropColumn('condition_type');
        });
    }
}
