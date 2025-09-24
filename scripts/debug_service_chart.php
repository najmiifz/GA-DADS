<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Asset;

$allVehicles = Asset::where('tipe','Kendaraan')
    ->withSum('services as services_cost_sum', 'cost')
    ->withSum('reimburseRequests as reimburse_cost_sum', 'biaya')
    ->withSum('serviceRequests as service_requests_estimasi_sum', 'estimasi_harga')
    ->get();

$serviceData = $allVehicles->groupBy('jenis_aset')->map(function ($group) {
    return $group->sum(function ($a) {
        $services = $a->services_cost_sum ?? $a->total_servis ?? 0;
        $reimburse = $a->reimburse_cost_sum ?? 0;
        $sr = $a->service_requests_estimasi_sum ?? 0;
        return $services + $reimburse + $sr;
    });
})->toArray();

print_r($serviceData);
