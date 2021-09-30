<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScriptQuestionsConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('script_questions_conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('question_id');
            $table->string('tag');
            $table->enum('operator',['is_equal_to','is_not_equal_to','is_greater_than','is_less_than','exists','does_not_exists','string_contains','string_does_not_contains','matches_regex']);
            $table->string('comparison_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('script_questions_conditions');
    }
}
