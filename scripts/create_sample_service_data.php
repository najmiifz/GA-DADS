<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Asset;
use App\Models\ServiceRequest;
use Carbon\Carbon;

echo "=== Membuat Data Contoh Service Request ===\n\n";

// 1. Cari atau buat user PIC
$picUser = User::where('role', 'user')->first();
if (!$picUser) {
    echo "Membuat user PIC baru...\n";
    $picUser = User::create([
        'name' => 'PIC Kendaraan',
        'email' => 'pic@example.com',
        'password' => bcrypt('password'),
        'role' => 'user',
        'email_verified_at' => now()
    ]);
} else {
    echo "Menggunakan user PIC yang ada: {$picUser->name}\n";
}

// 2. Cari atau buat admin untuk approval
$admin = User::where('role', 'admin')->first();
if (!$admin) {
    echo "Membuat admin baru...\n";
    $admin = User::create([
        'name' => 'Admin System',
        'email' => 'admin.system@example.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
        'email_verified_at' => now()
    ]);
} else {
    echo "Menggunakan admin yang ada: {$admin->name}\n";
}

// 3. Buat atau update assets kendaraan
echo "\nMembuat/memperbarui data assets kendaraan...\n";

$assets = [
    [
        'jenis_aset' => 'Toyota Avanza',
        'merk' => 'Toyota',
        'serial_number' => 'AVANZA-001',
        'tipe' => 'Kendaraan',
        'pic' => $picUser->name,
        'user_id' => $picUser->id,
        'lokasi' => 'Jakarta Pusat',
        'project' => 'Operasional Kantor',
        'tahun_beli' => 2020,
        'tanggal_beli' => '2020-01-15',
        'harga_beli' => 185000000,
        'kondisi' => 'Baik',
        'keterangan' => 'Kendaraan operasional untuk kegiatan harian'
    ],
    [
        'jenis_aset' => 'Honda Civic',
        'merk' => 'Honda',
        'serial_number' => 'CIVIC-001',
        'tipe' => 'Kendaraan',
        'pic' => $picUser->name,
        'user_id' => $picUser->id,
        'lokasi' => 'Jakarta Selatan',
        'project' => 'Operasional Manager',
        'tahun_beli' => 2019,
        'tanggal_beli' => '2019-06-10',
        'harga_beli' => 320000000,
        'kondisi' => 'Baik',
        'keterangan' => 'Kendaraan dinas untuk keperluan manajemen'
    ],
    [
        'jenis_aset' => 'Mitsubishi Pajero Sport',
        'merk' => 'Mitsubishi',
        'serial_number' => 'PAJERO-001',
        'tipe' => 'Kendaraan',
        'pic' => $picUser->name,
        'user_id' => $picUser->id,
        'lokasi' => 'Jakarta Barat',
        'project' => 'Survey Lapangan',
        'tahun_beli' => 2021,
        'tanggal_beli' => '2021-03-20',
        'harga_beli' => 450000000,
        'kondisi' => 'Sangat Baik',
        'keterangan' => 'Kendaraan untuk kegiatan survey dan lapangan'
    ]
];

$createdAssets = [];
foreach ($assets as $assetData) {
    $asset = Asset::updateOrCreate(
        ['serial_number' => $assetData['serial_number']],
        $assetData
    );
    $createdAssets[] = $asset;
    echo "Asset: {$asset->jenis_aset} ({$asset->serial_number}) - ID: {$asset->id}\n";
}

// 4. Buat service requests dengan berbagai status
echo "\nMembuat pengajuan service requests...\n";

$serviceRequests = [
    [
        'asset_id' => $createdAssets[0]->id,
        'user_id' => $picUser->id,
        'jenis_service' => 'Service Rutin',
        'km_sekarang' => 45000,
        'deskripsi' => 'Service rutin 40.000 KM, ganti oli dan filter',
        'status' => 'pending',
        'estimasi_harga' => 500000,
        'foto_km' => ['km/sample_km_1.jpg'],
        'foto_estimasi' => ['estimates/sample_estimate_1.jpg'],
        'created_at' => Carbon::now()->subDays(2),
    ],
    [
        'asset_id' => $createdAssets[1]->id,
        'user_id' => $picUser->id,
        'jenis_service' => 'Perbaikan AC',
        'km_sekarang' => 32000,
        'deskripsi' => 'AC tidak dingin, perlu pengecekan freon dan kompresor',
        'status' => 'approved',
        'estimasi_harga' => 750000,
        'foto_km' => ['km/sample_km_2.jpg'],
        'foto_estimasi' => ['estimates/sample_estimate_2.jpg'],
        'approved_by' => $admin->id,
        'approved_at' => Carbon::now()->subDays(1),
        'tanggal_approval' => Carbon::now()->subDays(1),
        'created_at' => Carbon::now()->subDays(3),
    ],
    [
        'asset_id' => $createdAssets[2]->id,
        'user_id' => $picUser->id,
        'jenis_service' => 'Ganti Ban',
        'km_sekarang' => 78000,
        'deskripsi' => 'Ban depan sudah tipis dan perlu diganti',
        'status' => 'service_pending',
        'estimasi_harga' => 1200000,
        'foto_km' => ['km/sample_km_3.jpg'],
        'foto_estimasi' => ['estimates/sample_estimate_3.jpg'],
        'approved_by' => $admin->id,
        'approved_at' => Carbon::now()->subDays(1),
        'tanggal_approval' => Carbon::now()->subDays(1),
        'created_at' => Carbon::now()->subDays(4),
    ],
    [
        'asset_id' => $createdAssets[0]->id,
        'user_id' => $picUser->id,
        'jenis_service' => 'Service Berkala',
        'km_sekarang' => 20000,
        'deskripsi' => 'Service berkala 20.000 KM sesuai jadwal maintenance',
        'status' => 'service_completed',
        'estimasi_harga' => 600000,
        'foto_km' => ['km/sample_km_4.jpg'],
        'foto_estimasi' => ['estimates/sample_estimate_4.jpg'],
        'foto_struk_service' => ['service-receipts/sample_receipt_1.jpg'],
        'total_biaya' => 650000,
        'tanggal_selesai' => Carbon::now()->subDays(2),
        'keterangan_selesai' => 'Service selesai, semua komponen sudah dicek dan diganti sesuai kebutuhan',
        'approved_by' => $admin->id,
        'approved_at' => Carbon::now()->subDays(5),
        'tanggal_approval' => Carbon::now()->subDays(5),
        'created_at' => Carbon::now()->subDays(7),
    ],
    [
        'asset_id' => $createdAssets[1]->id,
        'user_id' => $picUser->id,
        'jenis_service' => 'Tune Up Engine',
        'km_sekarang' => 55000,
        'deskripsi' => 'Engine tune up lengkap untuk performa optimal',
        'status' => 'completed',
        'estimasi_harga' => 900000,
        'foto_km' => ['km/sample_km_5.jpg'],
        'foto_estimasi' => ['estimates/sample_estimate_5.jpg'],
        'foto_struk_service' => ['service-receipts/sample_receipt_2.jpg'],
        'foto_invoice' => ['invoices/sample_invoice_1.jpg'],
        'total_biaya' => 875000,
        'tanggal_selesai' => Carbon::now()->subDays(5),
        'keterangan_selesai' => 'Tune up engine selesai, performa mesin sudah optimal',
        'tanggal_verifikasi' => Carbon::now()->subDays(3),
        'catatan_verifikasi' => 'Service sudah selesai dengan baik, semua komponen berfungsi normal',
        'verified_by' => $admin->id,
        'approved_by' => $admin->id,
        'approved_at' => Carbon::now()->subDays(10),
        'tanggal_approval' => Carbon::now()->subDays(10),
        'created_at' => Carbon::now()->subDays(12),
    ]
];

foreach ($serviceRequests as $index => $requestData) {
    // Create the record then assign a structured nomor_pengajuan
    $sr = ServiceRequest::create($requestData);
    $sr->nomor_pengajuan = 'SR-' . $sr->created_at->format('Ym') . '-' . str_pad($sr->id, 4, '0', STR_PAD_LEFT);
    $sr->save();

    $serviceRequest = $sr;
    echo "Service Request #{$serviceRequest->nomor_pengajuan}: {$serviceRequest->jenis_service} - Status: {$serviceRequest->status}\n";
}

echo "\n=== Data Contoh Berhasil Dibuat! ===\n";
echo "User PIC: {$picUser->name} ({$picUser->email})\n";
echo "Admin: {$admin->name} ({$admin->email})\n";
echo "Total Assets: " . count($createdAssets) . "\n";
echo "Total Service Requests: " . count($serviceRequests) . "\n";

echo "\n=== Ringkasan Status Service Requests ===\n";
$statuses = ServiceRequest::selectRaw('status, COUNT(*) as count')->groupBy('status')->get();
foreach ($statuses as $status) {
    echo "- {$status->status}: {$status->count} pengajuan\n";
}
