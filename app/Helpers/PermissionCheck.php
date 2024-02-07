<?php

namespace App\Helpers;

class PermissionCheck
{
    public function checkUser($link)
    {
        //Check if user is Admin
        $authcheck = auth()->user()->isAdmin();
        //Return True if user is Admin
        if ($authcheck) {
            return true;
        }
        //Check User Role and Permission
        $permission_check = auth()->user()->hasPermission($link);
        //Return True If User Has Role
        if ($permission_check) {
            return true;
        }
        //Return False When user doesnt have permission
        return false;
    }
}
