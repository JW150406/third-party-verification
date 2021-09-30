<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnIsIntroductionaryInScriptQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('script_questions', function (Blueprint $table) {
            $table->boolean('is_introductionary')->default(0)->nullable()->after('commodity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('script_questions', function (Blueprint $table) {
            $table->dropColumn('is_introductionary');
        });
    }
}
