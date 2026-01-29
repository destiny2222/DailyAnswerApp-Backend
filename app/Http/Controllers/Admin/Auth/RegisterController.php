<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $roles = Role::where('guard_name', 'admin')->get();

        return view('admin.auth.register', compact('roles'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
            'phone' => ['required', 'string', 'max:20', 'unique:admins'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'exists:roles,name'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $imagePath = 'avatar.png';
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('admins', 'public');
        }

        $admin = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'image' => $imagePath,
        ]);

        // Assign role
        $admin->assignRole($validated['role']);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Admin registered successfully.');
    }
}
