<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AssetPageController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\ReimburseRequestController;
use App\Http\Controllers\SpjRequestController;

// Ensure asset route parameters are numeric so /assets/create isn't caught by the show route
Route::pattern('asset', '[0-9]+');
// Ensure serviceRequest route parameters are numeric so JSON endpoint isn't caught by show route
Route::pattern('serviceRequest', '[0-9]+');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', function () {
    return redirect()->route('login');
});

// TEMPORARY: Quick login for testing
Route::get('/test-login-pic', function () {
    $user = \App\Models\User::where('email', 'user@example.com')->first();
    if ($user) {
        auth()->login($user);
        return redirect('/dashboard')->with('success', 'Logged in as PIC Test User');
    }
    return redirect('/login')->with('error', 'PIC user not found');
});

Route::get('/test-login-admin', function () {
    $user = \App\Models\User::where('email', 'admin@example.com')->first();
    if ($user) {
        auth()->login($user);
        return redirect('/dashboard')->with('success', 'Logged in as Admin');
    }
    return redirect('/login')->with('error', 'Admin user not found');
});

// Debug route for service requests
Route::get('/debug-service-requests', function () {
    $user = auth()->user();
    if (!$user) return 'Not logged in';

    $allRequests = \App\Models\ServiceRequest::all();
    $userRequests = \App\Models\ServiceRequest::where('user_id', $user->id)->get();
    $pendingRequests = \App\Models\ServiceRequest::where('user_id', $user->id)->where('status', 'service_pending')->get();

    return [
        'current_user' => $user->email . ' (ID: ' . $user->id . ')',
        'user_role' => $user->role,
        'all_service_requests' => $allRequests->map(function($sr) {
            return $sr->nomor_pengajuan . ' - Status: ' . $sr->status . ' - User ID: ' . $sr->user_id;
        }),
        'user_service_requests' => $userRequests->map(function($sr) {
            return $sr->nomor_pengajuan . ' - Status: ' . $sr->status;
        }),
        'pending_requests' => $pendingRequests->map(function($sr) {
            return $sr->nomor_pengajuan . ' - Status: ' . $sr->status;
        })
    ];
})->middleware('auth');

// ==================== DASHBOARD & ASET ==================== //


// Protected routes requiring authentication
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Activity Logs
    Route::get('/activity-logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity.logs');

    // Asset view routes (accessible to all authenticated users)
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
    Route::get('/assets/{asset}/json', [AssetController::class, 'showJson'])->name('assets.showJson');
    Route::get('/vehicles', [AssetController::class, 'vehicles'])->name('assets.vehicles');
    Route::get('/export', [AssetController::class, 'export'])->name('assets.export');
    Route::get('/export/vehicles', [AssetController::class, 'exportVehicles'])->name('assets.export.vehicles');
    // Splicers page removed
    Route::get('/assets/{asset}/export-pajak', [AssetController::class, 'exportPajak'])->name('assets.export.pajak');
    Route::get('/assets/{asset}/export-servis', [AssetController::class, 'exportServis'])->name('assets.export.servis');
    // Export single asset detail (metadata + services) as CSV for Excel
    Route::get('/assets/{asset}/export-csv', [AssetController::class, 'exportDetailCsv'])->name('assets.export.csv');

    // other routes
    Route::get('/search/advanced', function () { return view('search.advanced'); })->name('search.advanced');
    Route::get('/reports', function () { return view('reports.index'); })->name('reports.index');
    Route::get('/users', function () { return view('users.index'); })->name('users.index');
    // options API
    Route::get('/options/{category}', [App\Http\Controllers\OptionController::class, 'index']);

    // Rute untuk mengelola halaman aset kustom (view only for regular users)
    Route::get('/halaman/{slug}', [AssetPageController::class, 'show'])->name('asset-pages.show');
    Route::get('/halaman/{slug}/export-csv', [AssetPageController::class, 'exportCsv'])->name('asset-pages.export-csv');

    // Mark all notifications as read
    Route::post('/notifications/mark-all-read', function () {
        $user = auth()->user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }
        return redirect()->back();
    })->name('notifications.markAllRead');
});

// Asset CRUD routes (accessible to all authenticated users)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
    Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
    Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
    Route::delete('/assets/bulk-delete', [AssetController::class, 'bulkDestroy'])->name('assets.bulk-delete');

    // Holder history endpoints (previous holders)
    Route::post('/assets/{asset}/holders', [AssetController::class, 'storeHolder'])->name('assets.holders.store');
    Route::delete('/holders/{id}', [AssetController::class, 'deleteHolder'])->name('holders.destroy');

    // Options management
    Route::post('/options/{category}', [App\Http\Controllers\OptionController::class, 'store']);
});

// Asset Pages management routes (only for admin)
Route::middleware(['auth', 'verified', 'can:kelola-akun'])->group(function () {
    Route::get('/kelola-halaman', [AssetPageController::class, 'index'])->name('asset-pages.index');
    Route::get('/asset-pages/create', [AssetPageController::class, 'create'])->name('asset-pages.create');
    Route::post('/asset-pages', [AssetPageController::class, 'store'])->name('asset-pages.store');
    Route::get('/asset-pages/{assetPage}/edit', [AssetPageController::class, 'edit'])->name('asset-pages.edit');
    Route::put('/asset-pages/{assetPage}', [AssetPageController::class, 'update'])->name('asset-pages.update');
    Route::delete('/asset-pages/{assetPage}', [AssetPageController::class, 'destroy'])->name('asset-pages.destroy');
});

require __DIR__.'/auth.php';

// Notifikasi routes
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', function () {
        $user = auth()->user();
        return view('notifications.index', ['notifications' => $user->unreadNotifications]);
    })->name('notifications.index');

    Route::post('/notifications/{notification}/mark-as-read', function ($notification) {
        $notification = auth()->user()->notifications()->where('id', $notification)->first();
        if ($notification) {
            $notification->markAsRead();
        }
        return redirect()->back();
    })->name('notifications.markAsRead');
});

Route::middleware(['auth', 'can:kelola-akun'])->group(function () {
    Route::resource('users', App\Http\Controllers\UserController::class);
});

// Profile management routes (accessible to all authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/kelola-akun', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    // Update profile (avatar upload)
    Route::put('/kelola-akun', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    // Change password
    Route::put('/kelola-akun/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
});

    // Service Request routes
Route::middleware(['auth'])->group(function () {
    // JSON endpoint for service request details via AJAX
    Route::get('service-requests/{serviceRequest}/json', [ServiceRequestController::class, 'showJson'])
        ->name('service-requests.showJson');
    // All Activities for Service Requests (accessible to authenticated users)
    Route::get('service-requests/all-activities', [ServiceRequestController::class, 'allActivities'])
        ->name('service-requests.all-activities');
    // Export all activities to CSV
    Route::get('service-requests/all-activities/export-csv', [ServiceRequestController::class, 'exportAllActivitiesCsv'])
        ->name('service-requests.all-activities.export-csv');
    // Resource routes for service requests
    Route::resource('service-requests', ServiceRequestController::class);

    // Single record export to CSV
    Route::get('service-requests/{serviceRequest}/export-csv', [ServiceRequestController::class, 'exportDetailCsv'])
        ->name('service-requests.export-csv');

    // Service pending routes
    Route::get('service-pending', [ServiceRequestController::class, 'servicePendingIndex'])->name('service-requests.service-pending');
    Route::get('service-history', [ServiceRequestController::class, 'serviceHistoryIndex'])->name('service-requests.service-history');
    // Export service history to CSV
    Route::get('service-history/export-csv', [ServiceRequestController::class, 'exportHistoryCsv'])
        ->name('service-requests.service-history.export-csv');
    Route::post('service-requests/{serviceRequest}/complete-service', [ServiceRequestController::class, 'completeServiceByUser'])->name('service-requests.complete-service');

    // Admin approval routes
    Route::middleware('can:kelola-akun')->group(function () {
    // Export all APD activities to CSV (admin all activities)
    Route::get('apd-requests-admin/export-csv', [App\Http\Controllers\ApdRequestController::class, 'exportAllActivitiesCsv'])
        ->name('apd-requests.admin-index.export-csv');
        Route::post('service-requests/{serviceRequest}/approve', [ServiceRequestController::class, 'approve'])->name('service-requests.approve');
        Route::post('service-requests/{serviceRequest}/reject', [ServiceRequestController::class, 'reject'])->name('service-requests.reject');
        Route::post('service-requests/{serviceRequest}/send-to-service-pending', [ServiceRequestController::class, 'sendToServicePending'])->name('service-requests.send-to-service-pending');
        Route::post('service-requests/{serviceRequest}/verify', [ServiceRequestController::class, 'verify'])->name('service-requests.verify');
        Route::post('service-requests/{serviceRequest}/complete', [ServiceRequestController::class, 'complete'])->name('service-requests.complete');
    });

        // Bulk delete service requests
        Route::delete('service-requests/bulk-delete', [ServiceRequestController::class, 'bulkDelete'])
            ->name('service-requests.bulk-delete');
        // Delete foto routes
    Route::delete('service-requests/{serviceRequest}/foto-km', [ServiceRequestController::class, 'deleteFotoKm'])->name('service-requests.delete-foto-km');
    Route::delete('service-requests/{serviceRequest}/foto-estimasi', [ServiceRequestController::class, 'deleteFotoEstimasi'])->name('service-requests.delete-foto-estimasi');
});

    // APD and Reimburse Request routes (user)
Route::middleware(['auth'])->group(function () {
    // User APD request
    Route::get('apd-requests', [App\Http\Controllers\ApdRequestController::class, 'index'])->name('apd-requests.index');
    Route::get('apd-requests/create', [App\Http\Controllers\ApdRequestController::class, 'create'])->name('apd-requests.create');
    Route::get('apd-requests/new', [App\Http\Controllers\ApdRequestController::class, 'newRequest'])->name('apd-requests.new');
    Route::post('apd-requests', [App\Http\Controllers\ApdRequestController::class, 'store'])->name('apd-requests.store');

    Route::get('apd-requests/{apdRequest}', [App\Http\Controllers\ApdRequestController::class, 'show'])->name('apd-requests.show');
    // Export APD detail to CSV
    Route::get('apd-requests/{apdRequest}/export-csv', [App\Http\Controllers\ApdRequestController::class, 'exportDetailCsv'])->name('apd-requests.export-csv');
    // User: edit rejected APD requests
    Route::get('apd-requests/{apdRequest}/edit', [App\Http\Controllers\ApdRequestController::class, 'edit'])->name('apd-requests.edit');
    Route::put('apd-requests/{apdRequest}/update-user', [App\Http\Controllers\ApdRequestController::class, 'updateUser'])->name('apd-requests.update-user');
    // User: confirm receipt of delivered APD
    Route::post('apd-requests/{apdRequest}/receive', [App\Http\Controllers\ApdRequestController::class, 'receive'])->name('apd-requests.receive');
    // Endpoint to get next serial number for asset types
    Route::get('api/next-serial/{jenis}', [App\Http\Controllers\AssetController::class, 'nextSerial']);
    // User: confirm receipt of delivered APD
    // User: confirm receipt of delivered APD
    Route::get('apd-requests-history', [App\Http\Controllers\ApdRequestController::class, 'history'])->name('apd-requests.history');

    // User Reimburse request
    Route::get('reimburse-requests', [ReimburseRequestController::class, 'index'])->name('reimburse-requests.index');
        Route::get('reimburse-requests/create', [ReimburseRequestController::class, 'create'])->name('reimburse-requests.create');
    // Store reimburse request (asset_id passed via form)
    Route::post('reimburse-requests', [ReimburseRequestController::class, 'store'])->name('reimburse-requests.store');
    Route::get('reimburse-requests/{reimburseRequest}', [ReimburseRequestController::class, 'show'])->name('reimburse-requests.show');
    // Export reimburse detail to CSV
    Route::get('reimburse-requests/{reimburseRequest}/export-csv', [ReimburseRequestController::class, 'exportDetailCsv'])->name('reimburse-requests.export-csv');
    // User: edit rejected reimburse requests
    Route::get('reimburse-requests/{reimburseRequest}/edit', [ReimburseRequestController::class, 'edit'])->name('reimburse-requests.edit');
    Route::put('reimburse-requests/{reimburseRequest}/update-user', [ReimburseRequestController::class, 'updateUser'])->name('reimburse-requests.update-user');
    Route::get('reimburse-requests-history', [ReimburseRequestController::class, 'history'])->name('reimburse-requests.history');
});

// APD Request routes (admin approval)
Route::middleware(['auth', 'can:kelola-akun'])->group(function () {
    Route::get('apd-requests-admin', [App\Http\Controllers\ApdRequestController::class, 'indexAdmin'])->name('apd-requests.admin-index');
    // Bulk delete selected APD requests
    Route::delete('apd-requests-admin/bulk-delete', [App\Http\Controllers\ApdRequestController::class, 'bulkDelete'])->name('apd-requests.admin-index.bulk-delete');
    Route::get('apd-requests-admin-history', [App\Http\Controllers\ApdRequestController::class, 'adminHistory'])->name('apd-requests.admin-history');
    // Export APD admin history to CSV
    Route::get('apd-requests-admin-history/export-csv', [App\Http\Controllers\ApdRequestController::class, 'exportAdminHistoryCsv'])->name('apd-requests.admin-history.export-csv');
    // Bulk delete riwayat APD
    Route::delete('apd-requests-admin-history', [App\Http\Controllers\ApdRequestController::class, 'adminHistoryDelete'])->name('apd-requests.admin-history-delete');
    // Admin send/deliver APD via inline form
    Route::put('apd-requests/{apdRequest}', [App\Http\Controllers\ApdRequestController::class, 'update'])->name('apd-requests.update');
    // Admin approve (deduct stock and mark delivery)
    Route::post('apd-requests/{apdRequest}/approve', [App\Http\Controllers\ApdRequestController::class, 'approve'])->name('apd-requests.approve');
    Route::post('apd-requests/{apdRequest}/reject', [App\Http\Controllers\ApdRequestController::class, 'reject'])->name('apd-requests.reject');
        // Admin: restock items from an APD (manual restock action)
    Route::post('apd-requests/{apdRequest}/restock', [App\Http\Controllers\ApdRequestController::class, 'restock'])->name('apd-requests.restock');
    // Admin: reset semua stok APD ke nilai default 50
    Route::post('apd-requests/reset-stocks', [App\Http\Controllers\ApdRequestController::class, 'resetStocks'])->name('apd-requests.reset-stocks');
    // Admin: reset all APD stock items to default 50
    Route::post('apd-requests/reset-stocks', [App\Http\Controllers\ApdRequestController::class, 'resetStocks'])->name('apd-requests.reset-stocks');


    // Reimburse Request routes (admin approval)
    Route::get('reimburse-requests-admin', [ReimburseRequestController::class, 'indexAdmin'])->name('reimburse-requests.admin-index');
    // Export reimburse admin index to CSV
    Route::get('reimburse-requests-admin/export-csv', [ReimburseRequestController::class, 'exportAdminIndexCsv'])->name('reimburse-requests.admin-index.export-csv');
    // Bulk delete reimburse requests
    Route::delete('reimburse-requests-admin/bulk-delete', [ReimburseRequestController::class, 'bulkDelete'])->name('reimburse-requests.admin-index.bulk-delete');
    // Export reimburse admin index to CSV
    Route::get('reimburse-requests-admin/export-csv', [ReimburseRequestController::class, 'exportAdminIndexCsv'])->name('reimburse-requests.admin-index.export-csv');
    Route::post('reimburse-requests/{reimburseRequest}/approve', [ReimburseRequestController::class, 'approve'])->name('reimburse-requests.approve');
    Route::post('reimburse-requests/{reimburseRequest}/reject', [ReimburseRequestController::class, 'reject'])->name('reimburse-requests.reject');
});

// SPJ Request routes
Route::middleware('auth')->group(function() {
    // SPJ request for users
    Route::get('spj-requests', [SpjRequestController::class, 'userIndex'])->name('spj-requests.index');
    Route::get('spj/create', [SpjRequestController::class, 'create'])->name('spj.create');
    Route::post('spj', [SpjRequestController::class, 'store'])->name('spj.store');

    // Admin views
    Route::middleware('can:kelola-akun')->group(function() {
        Route::get('spj', [SpjRequestController::class, 'index'])->name('spj.index');
    Route::get('spj/{spjRequest}', [SpjRequestController::class, 'show'])->name('spj.show');
    Route::post('spj/{spjRequest}/approve', [SpjRequestController::class, 'approve'])->name('spj.approve');
    Route::post('spj/{spjRequest}/reject', [SpjRequestController::class, 'reject'])->name('spj.reject');
    // SPJ pending view
    Route::get('spj-pending', [SpjRequestController::class, 'index'])->defaults('status','pending')->name('spj.pending');
    });

    // SPJ history for all users (view only own or all depending on controller logic)
    Route::get('spj-history', [SpjRequestController::class, 'history'])->name('spj.history');
    // User can view their own SPJ detail
    Route::get('spj/{spjRequest}/view', [SpjRequestController::class, 'userShow'])->name('spj.view');
    // User: export SPJ detail as CSV
    Route::get('spj/{spjRequest}/export-csv', [SpjRequestController::class, 'exportCsv'])->name('spj.export-csv');
    // User: edit rejected SPJ requests
    Route::get('spj/{spjRequest}/edit', [SpjRequestController::class, 'edit'])->name('spj.edit');
    Route::put('spj/{spjRequest}/update-user', [SpjRequestController::class, 'updateUser'])->name('spj.update-user');
});

