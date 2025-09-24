#!/usr/bin/env php
<?php

// Change to the Laravel project directory
chdir(__DIR__);

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Asset;

echo "=== DEBUGGING 403 ERROR ===\n\n";

// Check PIC user
$picUser = User::where('email', 'pic@test.com')->first();
if (!$picUser) {
    echo "❌ PIC user tidak ditemukan\n";
    exit;
}

echo "👤 PIC User Info:\n";
echo "  - ID: {$picUser->id}\n";
echo "  - Email: {$picUser->email}\n";
echo "  - Name: {$picUser->name}\n";
echo "  - Role: {$picUser->role}\n\n";

// Check asset 49
$asset = Asset::find(49);
if (!$asset) {
    echo "❌ Asset 49 tidak ditemukan\n";
    exit;
}

echo "📦 Asset 49 Info:\n";
echo "  - ID: {$asset->id}\n";
echo "  - Merk: {$asset->merk}\n";
echo "  - Tipe: {$asset->tipe}\n";
echo "  - User ID: " . ($asset->user_id ?? 'NULL') . "\n";

if ($asset->user_id) {
    $assignedUser = User::find($asset->user_id);
    echo "  - Assigned to: {$assignedUser->name} ({$assignedUser->email})\n";
} else {
    echo "  - Status: UNASSIGNED ❌\n";
}

echo "\n🔍 Access Check:\n";
if ($asset->user_id === $picUser->id) {
    echo "✅ Asset IS assigned to PIC user - should work\n";
} else {
    echo "❌ Asset NOT assigned to PIC user - this is the problem!\n";
    echo "   - Asset user_id: {$asset->user_id}\n";
    echo "   - PIC user id: {$picUser->id}\n";
    echo "   - Let's fix this...\n";

    $asset->user_id = $picUser->id;
    $asset->save();
    echo "✅ FIXED: Asset now assigned to PIC user\n";
}

echo "\n📋 All Assets for PIC user:\n";
$userAssets = Asset::where('user_id', $picUser->id)->get();
foreach ($userAssets as $a) {
    echo "  - Asset {$a->id}: {$a->merk} {$a->tipe}\n";
}

echo "\n🔐 Role Check:\n";
if ($picUser->role === 'admin') {
    echo "✅ User is admin - can edit all assets\n";
} elseif ($picUser->role === 'user') {
    echo "✅ User is PIC - can only edit assigned assets\n";
} else {
    echo "❌ Unknown role: {$picUser->role}\n";
}

echo "\n🚀 Test URLs:\n";
echo "  - Login: http://127.0.0.1:8000/test-login-pic\n";
echo "  - Edit Asset 49: http://127.0.0.1:8000/assets/49/edit\n";
echo "  - Dashboard: http://127.0.0.1:8000/dashboard\n";

echo "\nDone!\n";
