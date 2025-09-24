<?php

// Script untuk menambahkan data test kendaraan dengan pajak jatuh tempo
// Jalankan dengan: php scripts/create_test_data.php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Asset;
use Carbon\Carbon;

// Buat kendaraan dengan pajak jatuh tempo dalam 7 hari
$testAsset = Asset::create([
    'jenis_aset' => 'Kendaraan',
    'merk' => 'Toyota',
    'model' => 'Avanza',
    'serial_number' => 'B 1234 TEST',
    'harga_beli' => 150000000,
    'tahun_beli' => 2020,
    'tanggal_beli' => '2020-01-15',
    'tipe' => 'MPV',
    'lokasi' => 'Jakarta',
    'project' => 'Test Project',
    'pic' => 'Admin Test',
    'kondisi' => 'Baik',
    'keterangan' => 'Data test untuk notifikasi pajak',
    'status' => 'Aktif',
    'harga_sewa' => 500000,
    'status_pajak' => 'Aktif',
    'tanggal_pajak' => Carbon::now()->addDays(7)->toDateString(), // 7 hari dari sekarang
    'jumlah_pajak' => 2500000,
    'total_servis' => 0
]);

echo "Data test berhasil dibuat!\n";
echo "ID Asset: " . $testAsset->id . "\n";
echo "Merk/Model: " . $testAsset->merk . " " . $testAsset->model . "\n";
echo "Nomor Polisi: " . $testAsset->serial_number . "\n";
echo "Tanggal Pajak: " . $testAsset->tanggal_pajak . "\n";
echo "\nSekarang jalankan command: php artisan pajak:check-jatuh-tempo\n";
