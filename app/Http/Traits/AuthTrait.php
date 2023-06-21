<?php

namespace App\Http\Traits;

use App\Http\Controllers\Api\Modules\Roles\Role;
use PHPUnit\Exception;

trait AuthTrait
{
    public function authUser($permission)
    {
        try {
            $role = Role::with('permissions')->find(auth('sanctum')->user()->role_id);
            for ($i = 0; $i < count($role->permissions); $i++) {
                $userpermissions[$i] = $role->permissions[$i]['name'];
            }
            return (in_array($permission, $userpermissions));
        } catch (Exception $e) {
        }
    }
}
