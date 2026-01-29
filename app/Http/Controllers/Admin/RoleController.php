<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends BaseAdminController
{
    public function index()
    {
        $roles = Role::where('guard_name', 'admin')->withCount('permissions')
            ->paginate(15);

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::where('guard_name', 'admin')->get();

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'admin',
        ]);

        if (! empty($validated['permissions'])) {
            $permissions = Permission::whereIn('id', $validated['permissions'])->get();
            $role->syncPermissions($permissions);
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function show(Role $role)
    {
        $role->load('permissions');

        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::where('guard_name', 'admin')->get();
        $role->load('permissions');

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$role->id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update(['name' => $validated['name']]);

        $permissions = Permission::whereIn('id', $validated['permissions'] ?? [])->get();
        $role->syncPermissions($permissions);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        // Prevent deleting default roles
        if (in_array($role->name, ['publisher', 'editor', 'writer', 'viewer'])) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'Cannot delete default system roles.');
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
