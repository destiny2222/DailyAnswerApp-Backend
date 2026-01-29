<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class BaseAdminController extends Controller
{
    /**
     * Check if admin has any of the given permissions.
     */
    protected function hasAnyPermission(array $permissions): bool
    {
        return auth('admin')->user()?->hasAnyPermission($permissions) ?? false;
    }

    /**
     * Check if admin has all of the given permissions.
     */
    protected function hasAllPermissions(array $permissions): bool
    {
        return auth('admin')->user()?->hasAllPermissions($permissions) ?? false;
    }
}
