<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create permissions
        Permission::firstOrCreate(['name' => 'kelola-akun']);
        Permission::firstOrCreate(['name' => 'kelola-aset']);
        Permission::firstOrCreate(['name' => 'kelola-servis']);
        Permission::firstOrCreate(['name' => 'kelola-pajak']);
        Permission::firstOrCreate(['name' => 'kelola-splicer']);
        Permission::firstOrCreate(['name' => 'kelola-kendaraan']);

        // create roles and assign existing permissions
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleAdmin->givePermissionTo('kelola-akun');
        $roleAdmin->givePermissionTo('kelola-aset');
        $roleAdmin->givePermissionTo('kelola-servis');
        $roleAdmin->givePermissionTo('kelola-pajak');
        $roleAdmin->givePermissionTo('kelola-splicer');
        $roleAdmin->givePermissionTo('kelola-kendaraan');

        $roleUser = Role::firstOrCreate(['name' => 'user']);
        // User PIC diberikan permission kelola-aset untuk asset yang ditugaskan
        $roleUser->givePermissionTo('kelola-aset');

        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        // gets all permissions via Gate::before rule; see AuthServiceProvider

        // create demo users
        $userSuperAdmin = \App\Models\User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super-Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'role' => 'super-admin',
            ]
        );
        $userSuperAdmin->assignRole($roleSuperAdmin);

        $userAdmin = \App\Models\User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );
        $userAdmin->assignRole($roleAdmin);

        $userRegular = \App\Models\User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'role' => 'user',
            ]
        );
        $userRegular->assignRole($roleUser);
    }
}
