<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin@amadtech.local');
        $admin = User::where('email', $adminEmail)->first();

        if (!$admin) {
            User::create([
                'name' => 'Admin Amadtech',
                'email' => $adminEmail,
                'password' => Hash::make(env('ADMIN_PASSWORD', 'AdminPass123!')),
                'role' => 'admin',
                'is_active' => true,
                'is_blocked' => false,
            ]);
            $this->command->info('Admin user created: ' . $adminEmail);
        } else {
            $admin->update(['role' => 'admin', 'is_active' => true, 'is_blocked' => false]);
            $this->command->info('Admin user updated: ' . $adminEmail);
        }
    }
}
