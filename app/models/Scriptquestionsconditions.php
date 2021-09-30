<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Scriptquestionsconditions extends Model
{
    protected $table = 'script_questions_conditions';
    protected $fillable = [
        'question_id', 'tag', 'operator','comparison_value','condition_type'];
    
}
