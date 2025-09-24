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

echo "Manually assigning specific assets...\n";

// Get specific PIC users
$picTest = User::where('email', 'pic@test.com')->first();
$picJakarta = User::where('email', 'pic.jakarta@test.com')->first();

if (!$picTest || !$picJakarta) {
    echo "PIC users not found!\n";
    exit;
}

// Get some assets and assign them
$assets = Asset::take(10)->get();
foreach ($assets as $index => $asset) {
    if ($index % 2 == 0) {
        $asset->user_id = $picTest->id;
        $assignTo = $picTest->name;
    } else {
        $asset->user_id = $picJakarta->id;
        $assignTo = $picJakarta->name;
    }

    $asset->save();
    echo "✓ Assigned Asset {$asset->id} ({$asset->merk} {$asset->tipe}) to {$assignTo}\n";
}

// Show final assignments
echo "\nFinal asset assignments by user:\n";
$users = User::where('role', 'user')->with('assets')->get();
foreach ($users as $user) {
    echo "\n{$user->name} ({$user->email}):\n";
    if ($user->assets->count() > 0) {
        foreach ($user->assets as $asset) {
            echo "  - Asset {$asset->id}: {$asset->merk} {$asset->tipe}\n";
        }
    } else {
        echo "  - No assets assigned\n";
    }
}

echo "\nDone!\n";
