<?php

namespace App\models;

use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
	public function accessLevels() {
        return $this->hasMany(PermissionAccessLevel::class);
    }

    public function roleClientSpecific() {
        return $this->hasMany(PermissionRoleClientSpecific::class);
    }
}
