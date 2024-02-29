<?php

namespace Modules\BookStack\Auth\Permissions;

use Modules\BookStack\Model;
use Modules\BookStack\Auth\Role;

class RolePermission extends Model
{
    protected $table = 'permissions';

    /**
     * The roles that belong to the permission.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role', 'permission_id', 'role_id');
    }

    /**
     * Get the permission object by name.
     *
     * @param mixed $name
     *
     * @return mixed
     */
    public static function getByName($name)
    {
        return static::where('name', '=', $name)->first();
    }
}
