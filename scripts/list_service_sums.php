<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ServiceHistory;
use App\Models\Asset;

$serviceSums = ServiceHistory::selectRaw('asset_id, SUM(cost) as total_cost, COUNT(*) as cnt')
    ->groupBy('asset_id')
    ->orderBy('total_cost', 'desc')
    ->get();

print "Service history sums (asset_id => total_cost, count):\n";
foreach ($serviceSums as $s) {
    $asset = Asset::find($s->asset_id);
    $merk = $asset ? ($asset->merk ?? 'Unknown') : 'MissingAsset';
    printf("%d => %s (Rp %s) [%d entries]\n", $s->asset_id, $merk, number_format($s->total_cost,0,',','.'), $s->cnt);
}
