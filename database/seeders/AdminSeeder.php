<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@amadtech.ai'],
            [
                'name' => 'Admin Amadtech',
                'password' => Hash::make('Admin@2025'),
                'role' => 'admin',
                'is_active' => true,
                'is_blocked' => false,
                'email_verified_at' => now(),
            ]
        );

        echo "✓ Admin user created with email: admin@amadtech.ai\n";
        echo "  Password: Admin@2025\n";
    }
}
