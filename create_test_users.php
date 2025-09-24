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

echo "Creating test PIC users...\n";

// Create PIC user 1
try {
    $picUser1 = User::firstOrCreate(
        ['email' => 'pic@test.com'],
        [
            'name' => 'PIC Test User',
            'password' => Hash::make('password123'),
            'role' => 'user'
        ]
    );
    echo "✓ PIC User 1 created/found: {$picUser1->name} ({$picUser1->email})\n";
} catch (Exception $e) {
    echo "✗ Error creating PIC User 1: " . $e->getMessage() . "\n";
}

// Create PIC user 2
try {
    $picUser2 = User::firstOrCreate(
        ['email' => 'pic.jakarta@test.com'],
        [
            'name' => 'PIC Jakarta',
            'password' => Hash::make('password123'),
            'role' => 'user'
        ]
    );
    echo "✓ PIC User 2 created/found: {$picUser2->name} ({$picUser2->email})\n";
} catch (Exception $e) {
    echo "✗ Error creating PIC User 2: " . $e->getMessage() . "\n";
}

// Show all users
echo "\nAll users in system:\n";
$users = User::all();
foreach ($users as $user) {
    echo "- {$user->name} ({$user->email}) - Role: {$user->role}\n";
}

echo "\nDone!\n";
