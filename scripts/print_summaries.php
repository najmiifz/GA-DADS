<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Asset;

$pj = Asset::selectRaw('project, count(*) as total')->groupBy('project')->pluck('total','project');
$js = Asset::selectRaw('jenis_aset, count(*) as total')->groupBy('jenis_aset')->pluck('total','jenis_aset');

echo "PROJECT SUMMARY:\n";
print_r($pj->toArray());

echo "\nJENIS SUMMARY:\n";
print_r($js->toArray());

echo "\n";
