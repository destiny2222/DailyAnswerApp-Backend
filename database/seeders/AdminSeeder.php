<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Admin',
                'phone' => '08079730678',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin123'),
                'role' => 'super-admin',
            ],
            [
                'name' => 'Moderator',
                'phone' => '08079730679',
                'email' => 'moderator@gmail.com',
                'password' => Hash::make('moderator123'),
                'role' => 'editor',
            ],
        ];

        foreach ($admins as $adminData) {
            $role = $adminData['role'];
            unset($adminData['role']);

            $admin = Admin::updateOrCreate(
                ['email' => $adminData['email']],
                $adminData
            );

            // Assign role using Spatie
            $admin->syncRoles([$role]);
        }
    }
}
