<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles & Permissions
        $superAdminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $managerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $userRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // 2. Super Admin User
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'is_active' => true,
                'email_verified_at' => now(),
                'last_login_at' => now(),
                'last_login_ip' => '127.0.0.1',
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        // 3. Regular Admin & Managers
        $admin = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Alex Manager',
                'password' => bcrypt('password'),
                'is_active' => true,
                'email_verified_at' => now(),
                'last_login_at' => now()->subHours(3),
                'last_login_ip' => '192.168.1.45',
            ]
        );
        $admin->assignRole($managerRole);

        // 4. Sample Users over past months for charts
        $sampleUsers = [
            ['name' => 'John Doe', 'email' => 'john@example.com', 'role' => $userRole, 'created_at' => now()->subMonths(5)],
            ['name' => 'Sarah Connor', 'email' => 'sarah@example.com', 'role' => $userRole, 'created_at' => now()->subMonths(4)],
            ['name' => 'Michael Scott', 'email' => 'michael@example.com', 'role' => $userRole, 'created_at' => now()->subMonths(3)],
            ['name' => 'Jim Halpert', 'email' => 'jim@example.com', 'role' => $userRole, 'created_at' => now()->subMonths(2)],
            ['name' => 'Pam Beesly', 'email' => 'pam@example.com', 'role' => $userRole, 'created_at' => now()->subMonths(1)],
            ['name' => 'Dwight Schrute', 'email' => 'dwight@example.com', 'role' => $adminRole, 'created_at' => now()],
        ];

        foreach ($sampleUsers as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => bcrypt('password'),
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'created_at' => $u['created_at'],
                ]
            );
            $user->assignRole($u['role']);
        }

        // 5. Sample Login Activities
        $agents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/128.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 Safari/17.5',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 Mobile/15E148',
            'Mozilla/5.0 (Linux; Android 14; Pixel 8) AppleWebKit/537.36 Chrome/128.0 Mobile Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:130.0) Gecko/20100101 Firefox/130.0',
        ];

        $users = User::all();
        foreach ($users as $idx => $usr) {
            \App\Models\LoginActivity::create([
                'user_id' => $usr->id,
                'ip_address' => '192.168.1.' . (10 + $idx),
                'city' => ['Ahmedabad', 'Mumbai', 'London', 'New York', 'Sydney'][$idx % 5],
                'country' => ['India', 'India', 'UK', 'USA', 'Australia'][$idx % 5],
                'user_agent' => $agents[$idx % count($agents)],
                'status' => 'success',
                'login_at' => now()->subHours($idx * 4),
            ]);
        }

        // Add a failed login attempt
        \App\Models\LoginActivity::create([
            'user_id' => $superAdmin->id,
            'ip_address' => '45.33.32.156',
            'city' => 'Frankfurt',
            'country' => 'Germany',
            'user_agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/119.0',
            'status' => 'failed',
            'login_at' => now()->subHours(12),
        ]);

        // 6. Default System Settings
        \App\Models\SystemSetting::set('app_name', 'Laravel 12 Starter', 'general', 'Application Name');
        \App\Models\SystemSetting::set('app_tagline', 'Modern Filament Admin Dashboard', 'general', 'Tagline');
        \App\Models\SystemSetting::set('support_email', 'admin@example.com', 'general', 'Support Email');
        \App\Models\SystemSetting::set('currency', 'USD ($)', 'general', 'Currency');
        \App\Models\SystemSetting::set('theme_color', 'amber', 'branding', 'Primary Theme Color');
        \App\Models\SystemSetting::set('maintenance_mode', '0', 'security', 'Maintenance Mode');
        \App\Models\SystemSetting::set('allow_registration', '1', 'security', 'Allow Registration');
    }
}
