<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class PermissionAccessLevel extends Model
{

    protected $fillable = ['permission_id','access_level'];

    public $timestamps = false;
}
