<?php
// Debug script to inspect service and reimburse totals for Kendaraan assets
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Asset;

$assets = Asset::where('tipe','Kendaraan')->with('services','reimburseRequests')->take(10)->get();
foreach ($assets as $a) {
    $servicesSum = $a->services->sum('cost');
    $reimburseSum = $a->reimburseRequests->sum('biaya');
    $servicesSumMethod = $a->services()->sum('cost');
    $reimburseSumMethod = $a->reimburseRequests()->sum('biaya');
    echo "ID: {$a->id}\n";
    echo "Jenis: " . ($a->jenis_aset ?? 'N/A') . "\n";
    echo "total_servis field: " . (($a->total_servis !== null) ? $a->total_servis : 'NULL') . "\n";
    echo "services collection sum: " . $servicesSum . "\n";
    echo "reimburse collection sum: " . $reimburseSum . "\n";
    echo "services()->sum(): " . $servicesSumMethod . "\n";
    echo "reimburseRequests()->sum(): " . $reimburseSumMethod . "\n";
    echo "attributes: " . json_encode($a->getAttributes()) . "\n";
    echo str_repeat('-',40) . "\n";
}
