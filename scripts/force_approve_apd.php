<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\ApdRequestController;
use App\Models\ApdRequest;

$id = $argv[1] ?? 1;
$apd = ApdRequest::find($id);
if (!$apd) { echo "ApdRequest $id not found\n"; exit(1); }
$controller = new ApdRequestController();
$controller->approve($apd);
echo "Called approve on APD id={$apd->id}\n";
