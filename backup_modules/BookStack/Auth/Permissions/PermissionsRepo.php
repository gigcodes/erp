<?php

namespace Modules\BookStack\Auth\Permissions;

use Illuminate\Support\Str;
use Modules\BookStack\Auth\Role;
use Modules\BookStack\Auth\Permissions;
use Modules\BookStack\Exceptions\PermissionsException;

class PermissionsRepo
{
    protected $permission;

    protected $role;

    protected $permissionService;

    protected $systemRoles = ['admin', 'public'];

    /**
     * PermissionsRepo constructor.
     *
     * @param \BookStack\Auth\Permissions\PermissionService $permissionService
     */
    public function __construct(RolePermission $permission, Role $role, Permissions\PermissionService $permissionService)
    {
        $this->permission        = $permission;
        $this->role              = $role;
        $this->permissionService = $permissionService;
    }

    /**
     * Get all the user roles from the system.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAllRoles()
    {
        return $this->role->all();
    }

    /**
     * Get all the roles except for the provided one.
     *
     * @return mixed
     */
    public function getAllRolesExcept(Role $role)
    {
        return $this->role->where('id', '!=', $role->id)->get();
    }

    /**
     * Get a role via its ID.
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function getRoleById($id)
    {
        return $this->role->findOrFail($id);
    }

    /**
     * Save a new role into the system.
     *
     * @param array $roleData
     *
     * @return Role
     */
    public function saveNewRole($roleData)
    {
        $role       = $this->role->newInstance($roleData);
        $role->name = str_replace(' ', '-', strtolower($roleData['display_name']));
        // Prevent duplicate names
        while ($this->role->where('name', '=', $role->name)->count() > 0) {
            $role->name .= strtolower(Str::random(2));
        }
        $role->save();

        $permissions = isset($roleData['permissions']) ? array_keys($roleData['permissions']) : [];
        $this->assignRolePermissions($role, $permissions);
        $this->permissionService->buildJointPermissionForRole($role);

        return $role;
    }

    /**
     * Updates an existing role.
     * Ensure Admin role always have core permissions.
     *
     *
     * @param mixed $roleId
     * @param mixed $roleData
     *
     * @throws PermissionsException
     */
    public function updateRole($roleId, $roleData)
    {
        $role = $this->role->findOrFail($roleId);

        $permissions = isset($roleData['permissions']) ? array_keys($roleData['permissions']) : [];
        if (strtolower($role->name) === 'admin') {
            $permissions = array_merge($permissions, [
                'users-manage',
                'user-roles-manage',
                'restrictions-manage-all',
                'restrictions-manage-own',
                'settings-manage',
            ]);
        }

        $this->assignRolePermissions($role, $permissions);

        $role->fill($roleData);
        $role->save();
        $this->permissionService->buildJointPermissionForRole($role);
    }

    /**
     * Assign an list of permission names to an role.
     *
     * @param array $permissionNameArray
     */
    public function assignRolePermissions(Role $role, $permissionNameArray = [])
    {
        $permissions         = [];
        $permissionNameArray = array_values($permissionNameArray);
        if ($permissionNameArray && count($permissionNameArray) > 0) {
            $permissions = $this->permission->whereIn('name', $permissionNameArray)->pluck('id')->toArray();
        }
        $role->permissions()->sync($permissions);
    }

    /**
     * Delete a role from the system.
     * Check it's not an admin role or set as default before deleting.
     * If an migration Role ID is specified the users assign to the current role
     * will be added to the role of the specified id.
     *
     *
     * @param mixed $roleId
     * @param mixed $migrateRoleId
     *
     * @throws PermissionsException
     */
    public function deleteRole($roleId, $migrateRoleId)
    {
        $role = $this->role->findOrFail($roleId);

        // Prevent deleting admin role or default registration role.
        if ($role->name && in_array(strtolower($role->name), $this->systemRoles)) {
            throw new PermissionsException(trans('bookstack::errors.role_system_cannot_be_deleted'));
        } elseif ($role->id == setting('registration-role')) {
            throw new PermissionsException(trans('bookstack::errors.role_registration_default_cannot_delete'));
        }

        if ($migrateRoleId) {
            $newRole = $this->role->find($migrateRoleId);
            if ($newRole) {
                $users = $role->users->pluck('id')->toArray();
                $newRole->users()->sync($users);
            }
        }

        $this->permissionService->deleteJointPermissionsForRole($role);
        $role->delete();
    }
}
