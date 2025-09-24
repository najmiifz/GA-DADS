<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = Illuminate\Http\Request::create('/service-requests/all-activities', 'GET');
$controller = new App\Http\Controllers\ServiceRequestController();
$response = $controller->allActivities($request);
if ($response instanceof Illuminate\Contracts\View\View) {
    echo "View: " . $response->getName() . "\n";
    $data = $response->getData();
    echo "Activities count: " . (isset($data['activities']) ? count($data['activities']) : 'n/a') . "\n";
} else {
    echo "Response type: " . get_class($response) . "\n";
}
