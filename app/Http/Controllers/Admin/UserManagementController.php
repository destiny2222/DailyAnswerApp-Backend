<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserManagementController extends BaseAdminController
{
    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(10);

        return view('admin.usersp.user', compact('users'));
    }

    public function create()
    {
        return view('admin.usersp.user-create');
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_staff' => ['nullable', 'boolean'],
        ]);

        $validated['is_staff'] = $request->has('is_staff');
        if ($validated['is_staff']) {
            $validated['has_paid'] = true;
            $validated['payment_expires_at'] = null;
        }

        try {
            User::create($validated);

            return redirect()->route('admin.customer.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while creating the user.');
        }
    }

    public function show(User $user)
    {
        return view('admin.usersp.user-show', compact('user'));
    }

    public function edit($id)
    {

        $user = User::findOrFail($id);

        return view('admin.usersp.user-edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            // 'username' => 'nullable|string|max:255|unique:users,username,'.$user->id,
        ]);

        try {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone; 
            $user->is_staff = $request->has('is_staff');
            
            if ($user->is_staff) {
                $user->has_paid = true;
                $user->payment_expires_at = null;
            }

            $user->save();

            return redirect()->route('admin.customer.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('error', 'An error occurred while updating the user.');
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return redirect()->route('admin.customer.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->route('admin.customer.index')->with('error', 'An error occurred while deleting the user.');
        }
    }
}
