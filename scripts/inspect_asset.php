<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Asset;

$id = $argv[1] ?? 15;
$a = Asset::where('id', $id)
    ->withSum('services as services_cost_sum', 'cost')
    ->withSum('reimburseRequests as reimburse_cost_sum', 'biaya')
    ->withSum('serviceRequests as service_requests_biaya_servis_sum', 'biaya_servis')
    ->withSum('serviceRequests as service_requests_estimasi_sum', 'estimasi_harga')
    ->first();

if (!$a) { echo "Asset $id not found\n"; exit; }

print "Asset {$a->id} - merk: " . ($a->merk ?? '(null)') . " - jenis_aset: " . ($a->jenis_aset ?? '(null)') . "\n";
print "services_cost_sum: " . ($a->services_cost_sum ?? 0) . "\n";
print "reimburse_cost_sum: " . ($a->reimburse_cost_sum ?? 0) . "\n";
print "service_requests_biaya_servis_sum: " . ($a->service_requests_biaya_servis_sum ?? 0) . "\n";
print "service_requests_estimasi_sum: " . ($a->service_requests_estimasi_sum ?? 0) . "\n";

// also list individual service_requests
foreach ($a->serviceRequests as $sr) {
    print "SR {$sr->id}: status={$sr->status}, estimasi={$sr->estimasi_harga}, biaya_servis={$sr->biaya_servis}\n";
}

// list services
foreach ($a->services as $s) {
    print "S {$s->id}: date={$s->service_date}, cost={$s->cost}\n";
}
