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

echo "=== TESTING USER EDIT PERMISSIONS ===\n\n";

// Get test PIC user
$picUser = User::where('email', 'pic@test.com')->first();
if (!$picUser) {
    echo "❌ PIC user not found\n";
    exit;
}

// Get assets assigned to this PIC
$assets = Asset::where('user_id', $picUser->id)->get();

echo "📋 Assets assigned to {$picUser->name}:\n";
foreach ($assets as $asset) {
    echo "  - Asset {$asset->id}: {$asset->merk} {$asset->tipe}\n";
    echo "    Edit URL: /assets/{$asset->id}/edit\n";
    echo "    Keterangan saat ini: " . ($asset->keterangan ?? 'Kosong') . "\n";
}

echo "\n🔧 User/PIC Edit Permissions:\n";
echo "✅ Dapat edit: Keterangan asset\n";
echo "✅ Dapat edit: Data pajak (tanggal, jumlah, status)\n";
echo "✅ Dapat edit: Riwayat servis (tambah, update)\n";
echo "❌ Tidak dapat edit: Tipe, Jenis, Merk, Serial Number\n";
echo "❌ Tidak dapat edit: Project, Lokasi, Harga\n";
echo "❌ Tidak dapat edit: Upload foto\n";
echo "❌ Tidak dapat edit: Assignment ke user lain\n";

echo "\n📝 Testing Instructions:\n";
echo "1. Login sebagai pic@test.com (password: password123)\n";
echo "2. Go to Dashboard → My Assets\n";
echo "3. Klik Edit pada salah satu asset\n";
echo "4. Hanya form sederhana yang akan muncul\n";
echo "5. Test edit keterangan dan simpan\n";

echo "\nDone!\n";
