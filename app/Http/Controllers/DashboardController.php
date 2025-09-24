<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\User;
use App\Models\ServiceRequest;
use App\Models\ApdRequest;
use App\Models\ReimburseRequest;
use App\Models\SpjRequest;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Jika user adalah admin, tampilkan dashboard admin dengan chart
        if ($user->role === 'admin') {
            return $this->adminDashboard();
        }

        // Jika user adalah PIC, tampilkan dashboard sederhana
        return $this->userDashboard();
    }

    private function adminDashboard()
    {
        // Data untuk admin dashboard (dengan chart dan statistik)
        $totalAssets = Asset::count();
        $totalUsers = User::where('role', 'user')->count();

        // Data untuk chart project
        $projectData = Asset::select('project', DB::raw('count(*) as total'))
            ->groupBy('project')
            ->pluck('total', 'project')
            ->toArray();

        // Data untuk chart tipe asset
        $tipeData = Asset::select('jenis_aset', DB::raw('count(*) as total'))
            ->groupBy('jenis_aset')
            ->pluck('total', 'jenis_aset')
            ->toArray();

        // Total nilai asset
        $totalNilai = Asset::sum('harga_beli') ?? 0;
    // Service request statistics (only pending)
    $totalServiceRequests = ServiceRequest::where('status', 'pending')->count();
    $pendingServiceRequests = $totalServiceRequests; // for consistency
    $historyServiceRequests = ServiceRequest::where('status', '!=', 'pending')->count();
    // APD, Reimburse, and SPJ statistics (only pending)
    $totalApdRequests = ApdRequest::where('status', 'pending')->count();
    $totalReimburseRequests = ReimburseRequest::where('status', 'pending')->count();
    $totalSpjRequests = \App\Models\SpjRequest::where('status', 'pending')->count();

        // Recent activities
        $recentServiceRequests = ServiceRequest::latest()->take(5)->get();
        $recentApdRequests = ApdRequest::latest()->take(5)->get();
        $recentReimburseRequests = ReimburseRequest::latest()->take(5)->get();

        return view('dashboard.admin', compact(
            'totalAssets',
            'totalUsers',
            'projectData',
            'tipeData',
            'totalNilai',
            'totalServiceRequests',
            'pendingServiceRequests',
            'historyServiceRequests',
            'totalApdRequests',
            'totalReimburseRequests',
            'totalSpjRequests',
            'recentServiceRequests',
            'recentApdRequests',
            'recentReimburseRequests'
        ));
    }

    private function userDashboard()
    {
        // Dashboard sederhana untuk user/PIC
        $assets = Asset::with('user')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalAssets = Asset::where('user_id', auth()->id())->count();

        return view('dashboard.user', compact('assets', 'totalAssets'));
    }
}
