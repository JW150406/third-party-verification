<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnIsMultipleInScriptQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('script_questions', function (Blueprint $table) {
            $table->boolean('is_multiple')->comment('1 - this question repeates multiple times in multiple lead enrollment 0 - this question doesnot repeat')->default(0)->after('is_customizable');
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
            $table->dropColumn('is_multiple');
        });
    }
}
