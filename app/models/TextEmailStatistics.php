<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class TextEmailStatistics extends Model
{
    protected $table = "text_email_statistics";
    protected $fillable = [
        'type',
    ];
}
