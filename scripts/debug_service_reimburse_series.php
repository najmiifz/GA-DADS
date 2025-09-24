<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Asset;

$allVehicles = Asset::where('tipe','Kendaraan')
    ->withSum('services as services_cost_sum', 'cost')
    ->withSum('reimburseRequests as reimburse_cost_sum', 'biaya')
    ->get();

$serviceTotals = $allVehicles->groupBy('jenis_aset')->mapWithKeys(function ($group, $key) {
    $total = $group->sum(function ($a) {
        return $a->services_cost_sum ?? $a->total_servis ?? 0;
    });
    $label = trim((string)$key) === '' ? 'Unknown' : $key;
    return [$label => $total];
})->toArray();

$reimburseTotals = $allVehicles->groupBy('jenis_aset')->mapWithKeys(function ($group, $key) {
    $total = $group->sum(function ($a) {
        return $a->reimburse_cost_sum ?? 0;
    });
    $label = trim((string)$key) === '' ? 'Unknown' : $key;
    return [$label => $total];
})->toArray();

// Filter out Unknown/empty keys to match controller's behavior
$serviceTotals = array_filter($serviceTotals, function($v, $k) {
    return trim((string)$k) !== '' && strtolower(trim((string)$k)) !== 'unknown';
}, ARRAY_FILTER_USE_BOTH);
$reimburseTotals = array_filter($reimburseTotals, function($v, $k) {
    return trim((string)$k) !== '' && strtolower(trim((string)$k)) !== 'unknown';
}, ARRAY_FILTER_USE_BOTH);

print "Service totals:\n"; print_r($serviceTotals);
print "Reimburse totals:\n"; print_r($reimburseTotals);
