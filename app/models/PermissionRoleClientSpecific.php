<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class PermissionRoleClientSpecific extends Model
{
    protected $table = 'permission_role_client_specific';
    protected $fillable = ['permission_id','client_id','role_id'];

    public $timestamps = false;
}
