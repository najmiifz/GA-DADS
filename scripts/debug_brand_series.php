<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Asset;

$allVehicles = Asset::where('tipe','Kendaraan')
    ->withSum('services as services_cost_sum', 'cost')
    ->withSum('reimburseRequests as reimburse_cost_sum', 'biaya')
    ->withSum('serviceRequests as service_requests_biaya_servis_sum', 'biaya_servis')
    ->withSum('serviceRequests as service_requests_estimasi_sum', 'estimasi_harga')
    ->get()
    ->filter(function ($a) {
        return trim((string)($a->merk ?? '')) !== '';
    });

$serviceTotals = $allVehicles->groupBy('merk')->mapWithKeys(function ($group, $key) {
    $total = $group->sum(function ($a) {
        $histories = (float) ($a->services_cost_sum ?? 0);
        $reqs = (float) ($a->service_requests_biaya_servis_sum ?? 0);
        if ($reqs <= 0) {
            $reqs = (float) ($a->service_requests_estimasi_sum ?? 0);
        }
        return $histories + $reqs;
    });
    $label = trim((string)$key) === '' ? 'Unknown' : $key;
    return [$label => $total];
})->toArray();

$reimburseTotals = $allVehicles->groupBy('merk')->mapWithKeys(function ($group, $key) {
    $total = $group->sum(function ($a) {
        return $a->reimburse_cost_sum ?? 0;
    });
    $label = trim((string)$key) === '' ? 'Unknown' : $key;
    return [$label => $total];
})->toArray();

// filter out zeros and Unknown
$serviceTotals = array_filter($serviceTotals, function($v,$k){ return trim((string)$k) !== '' && strtolower(trim((string)$k)) !== 'unknown' && $v>0; }, ARRAY_FILTER_USE_BOTH);
$reimburseTotals = array_filter($reimburseTotals, function($v,$k){ return trim((string)$k) !== '' && strtolower(trim((string)$k)) !== 'unknown' && $v>0; }, ARRAY_FILTER_USE_BOTH);

print "Service totals by merk:\n"; print_r($serviceTotals);
print "Reimburse totals by merk:\n"; print_r($reimburseTotals);
