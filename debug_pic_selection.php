<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUG PIC SELECTION ===\n";

// Check users
$users = App\Models\User::all();
echo "Total users: " . $users->count() . "\n";

foreach ($users as $user) {
    echo "User ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Role: {$user->role}\n";
}

// Check assets
$assets = App\Models\Asset::with('user')->get();
echo "\nTotal assets: " . $assets->count() . "\n";

foreach ($assets->take(5) as $asset) {
    echo "Asset ID: {$asset->id}, PIC: {$asset->pic}, User ID: {$asset->user_id}, User Name: " . ($asset->user ? $asset->user->name : 'null') . "\n";
}

echo "\n=== END DEBUG ===\n";
