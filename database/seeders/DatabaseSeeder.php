<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void {
        // Seed roles, permissions, and demo users
        $this->call(RolePermissionSeeder::class);
    // Seed assets
    $this->call(AssetSeeder::class);
    // Seed holder histories based on existing assets
    $this->call(HolderHistorySeeder::class);
    }
}
