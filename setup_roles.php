#!/usr/bin/env php
<?php

// Change to the Laravel project directory
chdir(__DIR__);

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Setting up user roles...\n";

// Make sure admin@example.com is admin role
$admin = User::where('email', 'admin@example.com')->first();
if ($admin) {
    $admin->role = 'admin';
    $admin->save();
    echo "✓ Set admin@example.com as admin role\n";
}

// Make sure admin-ga@dads.com is admin role
$superAdmin = User::where('email', 'admin-ga@dads.com')->first();
if ($superAdmin) {
    $superAdmin->role = 'admin';
    $superAdmin->save();
    echo "✓ Set admin-ga@dads.com as admin role\n";
}

echo "\nFinal user roles:\n";
$users = User::all();
foreach ($users as $user) {
    echo "- {$user->name} ({$user->email}) - Role: {$user->role}\n";
}

echo "\nDone!\n";
