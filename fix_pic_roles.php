<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

echo "=== FIXING PIC USER ROLES ===\n";

// Get all users without Spatie roles
$users = User::whereNotIn('id', function($query) {
    $query->select('model_id')
          ->from('model_has_roles')
          ->where('model_type', User::class);
})->get();

foreach ($users as $user) {
    if ($user->role === 'user') {
        $userRole = Role::findByName('user');
        $user->assignRole($userRole);
        echo "Assigned 'user' role to: {$user->name} ({$user->email})\n";
    } elseif ($user->role === 'admin') {
        $adminRole = Role::findByName('admin');
        $user->assignRole($adminRole);
        echo "Assigned 'admin' role to: {$user->name} ({$user->email})\n";
    }
}

echo "\n=== VERIFICATION ===\n";

$picUser = User::where('email', 'pic@test.com')->first();
if ($picUser) {
    echo "PIC Test User permissions:\n";
    echo "- Can kelola-aset: " . ($picUser->can('kelola-aset') ? 'YES' : 'NO') . "\n";
    echo "- Spatie roles: " . $picUser->getRoleNames()->implode(', ') . "\n";
}
