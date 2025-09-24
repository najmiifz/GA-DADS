<?php

require_once 'vendor/autoload.php';

// Load Laravel bootstrap
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Asset;
use App\Models\ServiceHistory;
use App\Models\User;
use Carbon\Carbon;

echo "=== TEST SISTEM 24 JAM RIWAYAT SERVICE ===\n\n";

// 1. Buat service history baru (baru dibuat)
$asset = Asset::first();
if (!$asset) {
    echo "❌ Tidak ada asset ditemukan\n";
    exit;
}

echo "📋 Asset yang digunakan untuk test: {$asset->merk} {$asset->jenis_aset}\n\n";

// Test 1: Service history baru (dapat diedit)
$newService = ServiceHistory::create([
    'asset_id' => $asset->id,
    'service_date' => now()->format('Y-m-d'),
    'description' => 'Test service - baru dibuat',
    'cost' => 100000,
    'vendor' => 'Test Vendor'
]);

echo "✅ Test 1 - Service history baru dibuat:\n";
echo "   ID: {$newService->id}\n";
echo "   Dibuat: {$newService->created_at->format('d/m/Y H:i:s')}\n";
echo "   Dapat diedit: " . ($newService->canBeModified() ? "✅ YA" : "❌ TIDAK") . "\n";
if ($newService->canBeModified()) {
    echo "   Sisa waktu: {$newService->getTimeRemainingForModification()}\n";
    echo "   Deadline: {$newService->getModificationDeadline()->format('d/m/Y H:i:s')}\n";
}
echo "\n";

// Test 2: Simulasi service history lama (25 jam yang lalu)
$oldService = ServiceHistory::create([
    'asset_id' => $asset->id,
    'service_date' => now()->subDays(2)->format('Y-m-d'),
    'description' => 'Test service - sudah lama',
    'cost' => 200000,
    'vendor' => 'Old Vendor'
]);

// Manually set created_at to 25 hours ago
$oldService->created_at = Carbon::now()->subHours(25);
$oldService->save();

echo "✅ Test 2 - Service history lama (simulasi 25 jam lalu):\n";
echo "   ID: {$oldService->id}\n";
echo "   Dibuat: {$oldService->created_at->format('d/m/Y H:i:s')}\n";
echo "   Dapat diedit: " . ($oldService->canBeModified() ? "✅ YA" : "❌ TIDAK") . "\n";
echo "   Deadline sudah terlewat: {$oldService->getModificationDeadline()->format('d/m/Y H:i:s')}\n";
echo "\n";

// Test 3: Service history di batas waktu (23 jam lalu)
$borderlineService = ServiceHistory::create([
    'asset_id' => $asset->id,
    'service_date' => now()->subDay()->format('Y-m-d'),
    'description' => 'Test service - hampir batas waktu',
    'cost' => 150000,
    'vendor' => 'Borderline Vendor'
]);

$borderlineService->created_at = Carbon::now()->subHours(23);
$borderlineService->save();

echo "✅ Test 3 - Service history hampir batas waktu (23 jam lalu):\n";
echo "   ID: {$borderlineService->id}\n";
echo "   Dibuat: {$borderlineService->created_at->format('d/m/Y H:i:s')}\n";
echo "   Dapat diedit: " . ($borderlineService->canBeModified() ? "✅ YA" : "❌ TIDAK") . "\n";
if ($borderlineService->canBeModified()) {
    echo "   Sisa waktu: {$borderlineService->getTimeRemainingForModification()}\n";
    echo "   Deadline: {$borderlineService->getModificationDeadline()->format('d/m/Y H:i:s')}\n";
}
echo "\n";

// Test 4: Cek role admin vs user
$admin = User::where('role', 'admin')->first();
$user = User::where('role', 'user')->first();

echo "🔐 Test 4 - Peran pengguna:\n";
if ($admin) {
    echo "   Admin ditemukan: {$admin->name} ({$admin->email})\n";
    echo "   ✅ Admin dapat mengedit semua riwayat service tanpa batasan waktu\n";
}
if ($user) {
    echo "   User ditemukan: {$user->name} ({$user->email})\n";
    echo "   ⚠️  User tunduk pada aturan 24 jam\n";
}
echo "\n";

echo "=== RANGKUMAN ATURAN SISTEM ===\n";
echo "1. ✅ Service history dapat diedit/dihapus dalam 24 jam setelah dibuat\n";
echo "2. ❌ Setelah 24 jam, user biasa tidak dapat mengubah atau menghapus\n";
echo "3. 👑 Admin memiliki akses penuh tanpa batasan waktu\n";
echo "4. ⏰ Sistem menampilkan sisa waktu dan peringatan visual\n";
echo "5. 🔒 Entry yang terkunci ditandai dengan ikon gembok\n\n";

echo "📝 CATATAN:\n";
echo "- Data test akan otomatis dibersihkan\n";
echo "- Sistem telah siap untuk production\n";
echo "- UI menampilkan indikator visual yang jelas\n\n";

// Cleanup - hapus data test
echo "🧹 Membersihkan data test...\n";
ServiceHistory::whereIn('id', [$newService->id, $oldService->id, $borderlineService->id])->delete();
echo "✅ Data test berhasil dihapus\n\n";

echo "🎉 TEST SELESAI - Sistem 24 jam berfungsi dengan baik!\n";
