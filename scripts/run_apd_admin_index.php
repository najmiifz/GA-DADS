<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = Illuminate\Http\Request::create('/apd-requests-admin', 'GET', ['status' => 'all']);
$controller = new App\Http\Controllers\ApdRequestController();
$response = $controller->indexAdmin($request);
if ($response instanceof Illuminate\Contracts\View\View) {
    $data = $response->getData();
    $requests = $data['requests'] ?? collect();
    $allRequests = $data['allRequests'] ?? collect();
    echo "requests count: " . count($requests) . "\n";
    echo "allRequests total: " . count($allRequests) . "\n";
    foreach (array_slice($requests->toArray(), 0, 5) as $r) {
        echo sprintf("ID:%s nomor:%s status:%s user_id:%s created:%s approved:%s\n", $r['id'], $r['nomor_pengajuan'], $r['status'], $r['user_id'] ?? '-', $r['created_at'] ?? '-', $r['approved_at'] ?? '-');
    }
} else {
    echo "Response type: " . get_class($response) . "\n";
}
