<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== DEBUG USER ROLES ===\n";

foreach (User::all() as $user) {
    echo "User: {$user->email}\n";
    echo "  - ID: {$user->id}\n";
    echo "  - Role column: {$user->role}\n";
    echo "  - Spatie Roles: " . $user->getRoleNames()->implode(', ') . "\n";
    echo "  - Has kelola-aset permission: " . ($user->can('kelola-aset') ? 'YES' : 'NO') . "\n";
    echo "  - Has kelola-akun permission: " . ($user->can('kelola-akun') ? 'YES' : 'NO') . "\n";
    echo "  - All permissions: " . $user->getAllPermissions()->pluck('name')->implode(', ') . "\n";
    echo "\n";
}

echo "=== ROLES IN DATABASE ===\n";
foreach (\Spatie\Permission\Models\Role::all() as $role) {
    echo "Role: {$role->name}\n";
    echo "  - Permissions: " . $role->permissions->pluck('name')->implode(', ') . "\n";
    echo "\n";
}
