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

echo "=== DEBUGGING ASSET ACCESS ===\n\n";

// Check asset 49
$asset = Asset::find(49);
if (!$asset) {
    echo "❌ Asset 49 not found\n";
    exit;
}

echo "📦 Asset 49 Details:\n";
echo "  - ID: {$asset->id}\n";
echo "  - Merk: {$asset->merk}\n";
echo "  - Tipe: {$asset->tipe}\n";
echo "  - User ID: " . ($asset->user_id ?? 'NULL') . "\n";

if ($asset->user_id) {
    $assignedUser = User::find($asset->user_id);
    echo "  - Assigned to: {$assignedUser->name} ({$assignedUser->email})\n";
} else {
    echo "  - Status: UNASSIGNED\n";
}

echo "\n👥 Available PIC Users:\n";
$picUsers = User::where('role', 'user')->get();
foreach ($picUsers as $user) {
    $assetCount = Asset::where('user_id', $user->id)->count();
    echo "  - {$user->name} ({$user->email}) - {$assetCount} assets\n";
}

echo "\n🔧 Let's assign asset 49 to PIC Test User:\n";
$picUser = User::where('email', 'pic@test.com')->first();
if ($picUser) {
    $asset->user_id = $picUser->id;
    $asset->save();
    echo "✅ Asset 49 assigned to {$picUser->name}\n";
} else {
    echo "❌ PIC user not found\n";
}

echo "\n📋 Updated Asset List for PIC Test User:\n";
$assets = Asset::where('user_id', $picUser->id)->get();
foreach ($assets as $asset) {
    echo "  - Asset {$asset->id}: {$asset->merk} {$asset->tipe}\n";
}

echo "\nDone!\n";
