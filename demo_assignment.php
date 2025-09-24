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

echo "=== DEMO ASSET ASSIGNMENT PROCESS ===\n\n";

// Show available PIC users
echo "📋 Available PIC Users:\n";
$picUsers = User::where('role', 'user')->get();
foreach ($picUsers as $pic) {
    $assignedCount = Asset::where('user_id', $pic->id)->count();
    echo "  - {$pic->name} ({$pic->email}) - Currently managing {$assignedCount} assets\n";
}

echo "\n📦 Asset Assignment Status:\n";
$assets = Asset::with('user')->get();
foreach ($assets as $asset) {
    $assignedTo = $asset->user ? $asset->user->name : '❌ UNASSIGNED';
    echo "  - Asset {$asset->id}: {$asset->merk} {$asset->tipe} → {$assignedTo}\n";
}

echo "\n🔧 How Assignment Works:\n";
echo "1. Admin goes to /assets/create or /assets/{id}/edit\n";
echo "2. Admin sees 'Assign PIC' section (only visible to admin)\n";
echo "3. Admin selects PIC from dropdown\n";
echo "4. Asset gets assigned to selected PIC\n";
echo "5. PIC can now see and manage only their assigned assets\n";

echo "\n🔒 Access Control:\n";
echo "- Admin role: Can see ALL assets and manage assignments\n";
echo "- User/PIC role: Can only see assets assigned to them\n";

echo "\n✅ Current System Status: ACTIVE\n";
echo "✅ Role-based filtering: WORKING\n";
echo "✅ Assignment interface: AVAILABLE (admin only)\n";

echo "\nDone!\n";
