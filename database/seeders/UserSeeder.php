<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // WARNING: These accounts are for DEVELOPMENT ONLY.
        // Change passwords before any production deployment.
        $users = [
            [
                'name'        => 'Dr. Ahmad Fauzan',
                'email'       => 'admin@example.com',
                'password'    => Hash::make('password'),
                'role'        => 'ADMIN',
                'department'  => 'Laboratory Management',
                'position'    => 'System Administrator',
                'status'      => 'active',
                'last_login_at' => now()->subHours(2),
            ],
            [
                'name'        => 'Sari Dewi Rahayu',
                'email'       => 'manager@example.com',
                'password'    => Hash::make('password'),
                'role'        => 'STOCK_MANAGER',
                'department'  => 'Inventory Control',
                'position'    => 'Stock Manager',
                'status'      => 'active',
                'last_login_at' => now()->subHours(5),
            ],
            [
                'name'        => 'Budi Santoso',
                'email'       => 'auditor@example.com',
                'password'    => Hash::make('password'),
                'role'        => 'AUDITOR',
                'department'  => 'Quality Assurance',
                'position'    => 'QA Auditor',
                'status'      => 'active',
                'last_login_at' => now()->subDays(1),
            ],
            [
                'name'        => 'Rina Kusuma',
                'email'       => 'viewer@example.com',
                'password'    => Hash::make('password'),
                'role'        => 'VIEWER',
                'department'  => 'Research & Development',
                'position'    => 'Research Associate',
                'status'      => 'active',
                'last_login_at' => now()->subDays(2),
            ],
            [
                'name'        => 'Fitria',
                'email'       => 'fitria@example.com',
                'password'    => Hash::make('password'),
                'role'        => 'STOCK_MANAGER',
                'department'  => 'Analytical Laboratory',
                'position'    => 'Analyst',
                'status'      => 'active',
                'last_login_at' => now()->subDays(1),
            ],
            [
                'name'        => 'Tyas',
                'email'       => 'tyas@example.com',
                'password'    => Hash::make('password'),
                'role'        => 'STOCK_MANAGER',
                'department'  => 'Analytical Laboratory',
                'position'    => 'Analyst',
                'status'      => 'active',
                'last_login_at' => now()->subHours(8),
            ],
            [
                'name'        => 'Nur Janah',
                'email'       => 'nurjanah@example.com',
                'password'    => Hash::make('password'),
                'role'        => 'STOCK_MANAGER',
                'department'  => 'Analytical Laboratory',
                'position'    => 'Analyst',
                'status'      => 'active',
                'last_login_at' => now()->subDays(3),
            ],
            [
                'name'        => 'Alya',
                'email'       => 'alya@example.com',
                'password'    => Hash::make('password'),
                'role'        => 'STOCK_MANAGER',
                'department'  => 'Analytical Laboratory',
                'position'    => 'Analyst',
                'status'      => 'active',
                'last_login_at' => now()->subHours(4),
            ],
            [
                'name'        => 'Gebrina',
                'email'       => 'gebrina@example.com',
                'password'    => Hash::make('password'),
                'role'        => 'STOCK_MANAGER',
                'department'  => 'Analytical Laboratory',
                'position'    => 'Analyst',
                'status'      => 'active',
                'last_login_at' => now()->subHours(6),
            ],
            [
                'name'        => 'Jihan',
                'email'       => 'jihan@example.com',
                'password'    => Hash::make('password'),
                'role'        => 'STOCK_MANAGER',
                'department'  => 'Analytical Laboratory',
                'position'    => 'Analyst',
                'status'      => 'active',
                'last_login_at' => now()->subDays(1),
            ],
            [
                'name'        => 'Fahmi',
                'email'       => 'fahmi@example.com',
                'password'    => Hash::make('password'),
                'role'        => 'STOCK_MANAGER',
                'department'  => 'Analytical Laboratory',
                'position'    => 'Analyst',
                'status'      => 'active',
                'last_login_at' => now()->subDays(1),
            ],
            [
                'name'        => 'Iseh',
                'email'       => 'iseh@example.com',
                'password'    => Hash::make('password'),
                'role'        => 'STOCK_MANAGER',
                'department'  => 'Analytical Laboratory',
                'position'    => 'Analyst',
                'status'      => 'active',
                'last_login_at' => now()->subDays(1),
            ],
            [
                'name'        => 'Bayu',
                'email'       => 'bayu@example.com',
                'password'    => Hash::make('password'),
                'role'        => 'STOCK_MANAGER',
                'department'  => 'Analytical Laboratory',
                'position'    => 'Analyst',
                'status'      => 'active',
                'last_login_at' => now()->subDays(1),
            ],
            [
                'name'        => 'Prapto',
                'email'       => 'prapto@example.com',
                'password'    => Hash::make('password'),
                'role'        => 'STOCK_MANAGER',
                'department'  => 'Analytical Laboratory',
                'position'    => 'Analyst',
                'status'      => 'active',
                'last_login_at' => now()->subDays(1),
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['email' => $user['email']], $user);
        }
    }
}
