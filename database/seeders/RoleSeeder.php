<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
         * Create the application roles.
         */
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $userRole = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web',
        ]);

        /*
         * If there are already users, make the first user
         * the Super Admin and give all remaining users
         * the normal User role if they don't have a role.
         */
        $users = User::orderBy('id')->get();

        if ($users->isNotEmpty()) {
            $firstUser = $users->first();

            $firstUser->syncRoles([$superAdminRole]);

            foreach ($users->skip(1) as $user) {
                if ($user->roles()->doesntExist()) {
                    $user->assignRole($userRole);
                }
            }
        } else {
            /*
             * If there are no users yet, create a default
             * Super Admin account.
             */
            $superAdmin = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);

            $superAdmin->assignRole($superAdminRole);
        }

        /*
         * Prevent unused variable warnings and make it clear
         * that all three roles are intentionally created.
         */
        $adminRole->refresh();
        $userRole->refresh();
    }
}