<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Models\ApdRequest;
use App\Models\ReimburseRequest;
use App\Models\SpjRequest;

class ActivityLogController extends Controller
{
    public function index()
    {
    $user = auth()->user();
    $userId = $user->id ?? null;
    $isAdmin = $user && in_array($user->role ?? '', ['admin', 'super-admin']);

        $logs = collect();

        // Service Requests
        $serviceQuery = ServiceRequest::query()
            ->when($userId && !$isAdmin, function($q) use ($userId) { return $q->where('user_id', $userId); })
            ->whereIn('status', ['pending', 'service_pending']);

        $serviceItems = $serviceQuery->with('asset')->latest()->take(50)->get()->map(function($r) {
                return (object)[
                'type' => 'Service',
                'id' => $r->id ?? null,
                'nomor' => $r->nomor_pengajuan ?? $r->nomor ?? null,
                'user' => $r->user ?? null,
                'asset' => optional($r->asset)->nama ?? ($r->asset_name ?? null),
                'project' => $r->lokasi_project ?? null,
                'lokasi' => optional($r->asset)->lokasi ?? null,
                'keterangan' => $r->keterangan ?? $r->deskripsi ?? null,
                'amount' => $r->total_biaya ?? $r->biaya ?? null,
                'status' => $r->status ?? null,
                'created_at' => $r->created_at,
                'link' => $this->makeLink('SR', $r->id),
            ];
        });

        $logs = $logs->merge($serviceItems);

        // APD Requests
        $apdQuery = ApdRequest::query()
            ->when($userId && !$isAdmin, function($q) use ($userId) { return $q->where('user_id', $userId); })
            ->whereIn('status', ['pending', 'delivery']);

    $apdItems = $apdQuery->latest()->take(50)->get()->map(function($r) {
        return (object)[
            'type' => 'APD',
            'id' => $r->id ?? null,
            'nomor' => $r->nomor_pengajuan ?? null,
            'user' => $r->user ?? null,
            'asset' => null,
            'project' => $r->lokasi_project ?? null,
            'lokasi' => $r->nama_cluster ?? null,
            'keterangan' => $r->keterangan ?? null,
            'amount' => $r->jumlah ?? null,
            'status' => $r->status ?? null,
            'created_at' => $r->created_at,
            'link' => $this->makeLink('APD', $r->id),
        ];
    });

        $logs = $logs->merge($apdItems);

        // Reimburse Requests
        $rbmQuery = ReimburseRequest::query()
            ->when($userId && !$isAdmin, function($q) use ($userId) { return $q->where('user_id', $userId); })
            ->whereIn('status', ['pending']);

    $rbmItems = $rbmQuery->with('asset')->latest()->take(50)->get()->map(function($r) {
            return (object)[
                'type' => 'Reimburse',
                'id' => $r->id ?? null,
                'nomor' => $r->nomor_pengajuan ?? null,
                'user' => $r->user ?? null,
                'asset' => optional($r->asset)->nama ?? ($r->asset_name ?? null),
                'project' => $r->lokasi_project ?? null,
                'lokasi' => optional($r->asset)->lokasi ?? null,
                'keterangan' => $r->keterangan ?? null,
                'amount' => $r->biaya ?? $r->amount ?? null,
                'status' => $r->status ?? null,
                'created_at' => $r->created_at,
                'link' => $this->makeLink('RBM', $r->id),
            ];
        });

        $logs = $logs->merge($rbmItems);

        // SPJ Requests
        $spjQuery = SpjRequest::query()
            ->when($userId && !$isAdmin, function($q) use ($userId) { return $q->where('user_id', $userId); })
            ->whereIn('status', ['pending']);

    $spjItems = $spjQuery->latest()->take(50)->get()->map(function($r) {
            return (object)[
                'type' => 'SPJ',
                'id' => $r->id ?? null,
                'nomor' => $r->nomor_pengajuan ?? null,
                'user' => $r->user ?? null,
                'asset' => optional($r->asset)->nama ?? ($r->asset_name ?? null),
                'project' => $r->lokasi_project ?? null,
                'lokasi' => optional($r->asset)->lokasi ?? null,
                'keterangan' => $r->keterangan ?? null,
                'amount' => $r->jumlah ?? null,
                'status' => $r->status ?? null,
                'created_at' => $r->created_at,
                'link' => $this->makeLink('SPJ', $r->id),
            ];
        });

        $logs = $logs->merge($spjItems);

        // sort by created_at desc
        $logs = $logs->sortByDesc('created_at')->values()->take(50);

        // Build history (completed/approved/rejected) items
        $history = collect();

        $finishedStatuses = ['approved','rejected','completed','service_completed','delivery'];

        // Service history
        $serviceHist = ServiceRequest::query()
            ->when($userId && !$isAdmin, function($q) use ($userId) { return $q->where('user_id', $userId); })
            ->whereIn('status', ['completed','service_completed','approved','rejected'])
            ->with('asset')
            ->latest()
            ->take(50)
            ->get()
            ->map(function($r) {
                return (object)[
                    'type' => 'Service',
                    'id' => $r->id ?? null,
                    'nomor' => $r->nomor_pengajuan ?? $r->nomor ?? null,
                    'user' => $r->user ?? null,
                    'asset' => optional($r->asset)->nama ?? ($r->asset_name ?? null),
                    'project' => $r->lokasi_project ?? null,
                    'lokasi' => optional($r->asset)->lokasi ?? null,
                    'keterangan' => $r->keterangan ?? $r->deskripsi ?? null,
                    'amount' => $r->total_biaya ?? $r->biaya ?? null,
                    'status' => $r->status ?? null,
                    'created_at' => $r->created_at,
                    'link' => $this->makeLink('SR', $r->id),
                ];
            });
        $history = $history->merge($serviceHist);

        // APD history
        $apdHist = ApdRequest::query()
            ->when($userId && !$isAdmin, function($q) use ($userId) { return $q->where('user_id', $userId); })
            ->whereIn('status', ['approved','rejected','delivery'])
            ->latest()
            ->take(50)
            ->get()
            ->map(function($r) {
                return (object)[
                    'type' => 'APD',
                    'id' => $r->id ?? null,
                    'nomor' => $r->nomor_pengajuan ?? null,
                    'user' => $r->user ?? null,
                    'asset' => null,
                    'project' => $r->lokasi_project ?? null,
                    'lokasi' => $r->nama_cluster ?? null,
                    'keterangan' => $r->keterangan ?? null,
                    'amount' => $r->jumlah ?? null,
                    'status' => $r->status ?? null,
                    'created_at' => $r->created_at,
                    'link' => $this->makeLink('APD', $r->id),
                ];
            });
        $history = $history->merge($apdHist);

        // Reimburse history
        $rbmHist = ReimburseRequest::query()
            ->when($userId && !$isAdmin, function($q) use ($userId) { return $q->where('user_id', $userId); })
            ->whereIn('status', ['approved','rejected'])
            ->with('asset')
            ->latest()
            ->take(50)
            ->get()
            ->map(function($r) {
                return (object)[
                    'type' => 'Reimburse',
                    'id' => $r->id ?? null,
                    'nomor' => $r->nomor_pengajuan ?? null,
                    'user' => $r->user ?? null,
                    'asset' => optional($r->asset)->nama ?? ($r->asset_name ?? null),
                    'project' => $r->lokasi_project ?? null,
                    'lokasi' => optional($r->asset)->lokasi ?? null,
                    'keterangan' => $r->keterangan ?? null,
                    'amount' => $r->biaya ?? $r->amount ?? null,
                    'status' => $r->status ?? null,
                    'created_at' => $r->created_at,
                    'link' => $this->makeLink('RBM', $r->id),
                ];
            });
        $history = $history->merge($rbmHist);

        // SPJ history
        $spjHist = SpjRequest::query()
            ->when($userId && !$isAdmin, function($q) use ($userId) { return $q->where('user_id', $userId); })
            ->whereIn('status', ['approved','rejected','completed'])
            ->latest()
            ->take(50)
            ->get()
            ->map(function($r) {
                return (object)[
                    'type' => 'SPJ',
                    'id' => $r->id ?? null,
                    'nomor' => $r->nomor_pengajuan ?? null,
                    'user' => $r->user ?? null,
                    'asset' => optional($r->asset)->nama ?? ($r->asset_name ?? null),
                    'project' => $r->lokasi_project ?? null,
                    'lokasi' => optional($r->asset)->lokasi ?? null,
                    'keterangan' => $r->keterangan ?? null,
                    'amount' => $r->jumlah ?? null,
                    'status' => $r->status ?? null,
                    'created_at' => $r->created_at,
                    'link' => $this->makeLink('SPJ', $r->id),
                ];
            });
        $history = $history->merge($spjHist);

        // sort history by created_at desc and limit
        $history = $history->sortByDesc('created_at')->values()->take(50);

        return view('activity_logs.index', compact('logs','history'));
    }

    protected function makeLink($type, $id)
    {
        switch (strtoupper($type)) {
            case 'SR': return route('service-requests.show', $id);
            case 'APD': return route('apd-requests.show', $id);
            case 'RBM': return route('reimburse-requests.show', $id);
            case 'SPJ': return route('spj.view', $id);
            default: return '#';
        }
    }
}
