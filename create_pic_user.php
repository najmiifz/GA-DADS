<?php

require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Create PIC user
$picUser = User::create([
    'name' => 'PIC Test User',
    'email' => 'pic@test.com',
    'password' => Hash::make('password123'),
    'role' => 'user' // or 'pic'
]);

echo "PIC user created with ID: " . $picUser->id . "\n";
echo "Email: " . $picUser->email . "\n";
echo "Role: " . $picUser->role . "\n";

// Create another PIC user
$picUser2 = User::create([
    'name' => 'PIC Jakarta',
    'email' => 'pic.jakarta@test.com',
    'password' => Hash::make('password123'),
    'role' => 'user'
]);

echo "PIC user 2 created with ID: " . $picUser2->id . "\n";
echo "Email: " . $picUser2->email . "\n";
echo "Role: " . $picUser2->role . "\n";
