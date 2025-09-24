#!/usr/bin/env php
<?php

// Change to the Laravel project directory
chdir(__DIR__);

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Asset;
use App\Models\User;

echo "Assigning assets to PIC users...\n";

// Get PIC users
$picUsers = User::where('role', 'user')->get();
echo "Found " . $picUsers->count() . " PIC users\n";

// Get some assets to assign
$assets = Asset::whereNull('user_id')->take(5)->get();
echo "Found " . $assets->count() . " unassigned assets\n";

if ($assets->count() > 0 && $picUsers->count() > 0) {
    foreach ($assets as $index => $asset) {
        $picUser = $picUsers[$index % $picUsers->count()]; // Round robin assignment

        $asset->user_id = $picUser->id;
        $asset->save();

        echo "✓ Assigned Asset '{$asset->merk} {$asset->tipe}' (ID: {$asset->id}) to {$picUser->name}\n";
    }
} else {
    echo "No assets or PIC users found for assignment\n";
}

// Show assignments
echo "\nCurrent asset assignments:\n";
$assignedAssets = Asset::whereNotNull('user_id')->with('user')->get();
foreach ($assignedAssets as $asset) {
    echo "- Asset {$asset->id} ({$asset->merk} {$asset->tipe}) → {$asset->user->name}\n";
}

echo "\nDone!\n";
