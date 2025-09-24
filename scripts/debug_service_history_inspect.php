<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ServiceRequest;

$rows = ServiceRequest::with('asset')->whereIn('status', ['completed','verified','done','selesai'])->orderBy('created_at','desc')->take(30)->get();
if ($rows->isEmpty()) {
    echo "No recent completed/verified service requests found. Listing latest 30 service requests instead.\n";
    $rows = ServiceRequest::with('asset')->orderBy('created_at','desc')->take(30)->get();
}

foreach ($rows as $r) {
    $asset = $r->asset;
    printf("SR ID:%s status:%s asset_id:%s asset_merk:%s asset_tipe:%s serial:%s\n", $r->id, $r->status, $r->asset_id ?? 'NULL', $asset ? $asset->merk : 'NULL', $asset ? $asset->tipe : 'NULL', $asset ? $asset->serial_number : 'NULL');
}
