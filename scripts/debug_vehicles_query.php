<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Asset;

$query = Asset::where('tipe','Kendaraan')
    ->with('user')
    ->withSum('services as services_cost_sum','cost')
    ->withSum('reimburseRequests as reimburse_cost_sum','biaya');

$vehicles = $query->paginate(10);
$v = $vehicles->items()[0] ?? null;
if (!$v) { echo "No vehicles\n"; exit; }
print_r($v->toArray());
