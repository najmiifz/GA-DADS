<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Asset;

$vehicles = Asset::where('tipe','Kendaraan')
    ->with('user')
    ->withSum('services as services_cost_sum','cost')
    ->withSum('reimburseRequests as reimburse_cost_sum','biaya')
    ->take(10)->get();

foreach ($vehicles as $v) {
    $jenis = strtolower($v->jenis_aset ?? '');
    $reimburseSum = $v->reimburse_cost_sum ?? $v->reimburseRequests()->sum('biaya');
    $servicesSum = $v->services_cost_sum ?? $v->services()->sum('cost') ?? $v->total_servis ?? 0;
    $display = (str_contains($jenis, 'motor')) ? $reimburseSum : $servicesSum;
    echo "ID: {$v->id} jenis={$v->jenis_aset} reimburseSum={$reimburseSum} servicesSum={$servicesSum} display={$display} formatted=Rp " . number_format(floatval($display ?? 0),0,',','.') . "\n";
}
