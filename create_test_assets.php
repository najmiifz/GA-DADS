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

echo "Creating test assets...\n";

// Get PIC users
$picTest = User::where('email', 'pic@test.com')->first();
$picJakarta = User::where('email', 'pic.jakarta@test.com')->first();

$testAssets = [
    [
        'tipe' => 'Elektronik',
        'jenis_aset' => 'Laptop',
        'pic' => 'John Doe',
        'merk' => 'Dell',
        'serial_number' => 'DL001',
        'project' => 'Project Alpha',
        'lokasi' => 'Jakarta',
        'user_id' => $picTest->id
    ],
    [
        'tipe' => 'Elektronik',
        'jenis_aset' => 'Laptop',
        'pic' => 'Jane Smith',
        'merk' => 'HP',
        'serial_number' => 'HP002',
        'project' => 'Project Beta',
        'lokasi' => 'Surabaya',
        'user_id' => $picJakarta->id
    ],
    [
        'tipe' => 'Kendaraan',
        'jenis_aset' => 'Mobil',
        'pic' => 'Bob Johnson',
        'merk' => 'Toyota',
        'serial_number' => 'TY003',
        'project' => 'Project Gamma',
        'lokasi' => 'Bandung',
        'user_id' => $picTest->id
    ],
    [
        'tipe' => 'Peralatan',
        'jenis_aset' => 'Printer',
        'pic' => 'Alice Brown',
        'merk' => 'Canon',
        'serial_number' => 'CN004',
        'project' => 'Project Delta',
        'lokasi' => 'Medan',
        'user_id' => $picJakarta->id
    ],
    [
        'tipe' => 'Elektronik',
        'jenis_aset' => 'Smartphone',
        'pic' => 'Charlie Wilson',
        'merk' => 'Samsung',
        'serial_number' => 'SM005',
        'project' => 'Project Echo',
        'lokasi' => 'Jakarta',
        'user_id' => null // Unassigned for admin testing
    ]
];

foreach ($testAssets as $assetData) {
    $asset = Asset::create($assetData);
    $assignedTo = $assetData['user_id'] ? User::find($assetData['user_id'])->name : 'Unassigned';
    echo "✓ Created Asset {$asset->id}: {$asset->merk} {$asset->tipe} (Assigned to: {$assignedTo})\n";
}

echo "\nTest assets created successfully!\n";

// Show final status
echo "\nFinal asset summary:\n";
$allAssets = Asset::with('user')->get();
foreach ($allAssets as $asset) {
    $assignedTo = $asset->user ? $asset->user->name : 'Unassigned';
    echo "- Asset {$asset->id}: {$asset->merk} {$asset->tipe} → {$assignedTo}\n";
}

echo "\nDone!\n";
