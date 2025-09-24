<?php
// Quick test for admin history page
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Models\User;
use App\Models\ApdRequest;
use Illuminate\Support\Facades\Auth;

// Login as admin
$admin = User::find(2);
Auth::login($admin);

echo "Testing admin history data:\n";
echo "===========================\n";

// Test the same query used in adminHistory method
$requests = ApdRequest::whereIn('status', ['approved', 'rejected'])->latest()->get();

echo "Total approved/rejected requests: " . $requests->count() . "\n";
echo "Recent approved requests:\n";

$approved = $requests->where('status', 'approved')->take(5);
foreach ($approved as $req) {
    echo "- {$req->nomor_pengajuan} - User: {$req->user->name} - Status: {$req->status} - Date: {$req->updated_at}\n";
}

echo "\nTesting route exists:\n";
try {
    $url = route('apd-requests.admin-history');
    echo "Admin history URL: {$url}\n";
} catch (Exception $e) {
    echo "Route error: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n";
