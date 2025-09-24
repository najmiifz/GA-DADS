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

echo "=== DAFTAR AKUN YANG TERSEDIA ===\n\n";

$users = User::all();

echo "👤 AKUN ADMIN:\n";
foreach ($users as $user) {
    if ($user->role === 'admin') {
        echo "  📧 Email: {$user->email}\n";
        echo "  👤 Nama: {$user->name}\n";
        echo "  🔑 Password: password (default Laravel)\n";
        echo "  🎯 Role: Admin (bisa akses semua fitur)\n";
        echo "  ➡️  URL Quick Login: http://127.0.0.1:8000/test-login-admin\n\n";
    }
}

echo "👥 AKUN PIC/USER:\n";
foreach ($users as $user) {
    if ($user->role === 'user') {
        $assetCount = Asset::where('user_id', $user->id)->count();
        echo "  📧 Email: {$user->email}\n";
        echo "  👤 Nama: {$user->name}\n";
        echo "  🔑 Password: password123 (untuk PIC baru) atau password (default)\n";
        echo "  🎯 Role: User/PIC (hanya bisa edit asset yang di-assign)\n";
        echo "  📦 Assets: {$assetCount} asset\n";
        if ($user->email === 'pic@test.com') {
            echo "  ➡️  URL Quick Login: http://127.0.0.1:8000/test-login-pic\n";
        }
        echo "\n";
    }
}

echo "🚀 CARA LOGIN CEPAT:\n";
echo "1. Admin: http://127.0.0.1:8000/test-login-admin\n";
echo "2. PIC: http://127.0.0.1:8000/test-login-pic\n";
echo "3. Manual: http://127.0.0.1:8000/login\n\n";

echo "📋 UNTUK TESTING EDIT ASSET:\n";
echo "1. Login sebagai PIC (pic@test.com)\n";
echo "2. Coba edit asset: http://127.0.0.1:8000/assets/49/edit\n";
echo "3. Hanya bisa edit: Keterangan, Pajak, Riwayat Servis\n\n";

echo "✅ SEMUA SIAP UNTUK TESTING!\n";
