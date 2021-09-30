<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salesagentlocation extends Model
{
    use SoftDeletes;
    
    protected $table = 'salesagentlocations';
    protected $fillable = [
        'salesagent_id',
        'lat',
        'lng'
    ];

    protected $casts = [
        'lat' => 'double',
        'lng' => 'double',
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'salesagent_id');
    }
}
