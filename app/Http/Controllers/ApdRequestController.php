<?php

namespace App\Http\Controllers;

use App\Models\ApdRequest;
use App\Models\User;
use App\Models\StockItem;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ApdRequestStatusNotification;
use App\Notifications\NewApdRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\PengajuanNumberService;
use Illuminate\Support\Facades\Log;

class ApdRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:kelola-akun')->only(['approve', 'reject', 'indexAdmin', 'adminHistory']);
    }

    // User: list own requests with filter
    public function index(Request $request)
    {
        // Default to pending if no status specified
        if (!$request->filled('status')) {
            return redirect()->route('apd-requests.index', ['status' => 'pending']);
        }
        $query = auth()->user()->apdRequests()->latest();
        // Show all if 'all', otherwise filter by status
        if ($request->status !== 'all') {
            $query->where('status', $request->status);
        }
        $requests = $query->get();
        return view('apd-requests.index', compact('requests'));
    }

    // User: show create form
    public function create()
    {
        // Prevent admin from creating APD requests
        if (auth()->user()->role === 'admin') {
            return redirect()->route('apd-requests.admin-index')
                ->with('error', 'Admin tidak dapat membuat pengajuan APD. Silakan gunakan menu Admin APD untuk mengelola pengajuan.');
        }

        // Prepare summary counts for create page
        $allRequests = auth()->user()->apdRequests()->get();
        $total = $allRequests->count();
        $pending = $allRequests->where('status', 'pending')->count();
        $delivery = $allRequests->where('status', 'delivery')->count();
        $approved = $allRequests->where('status', 'approved')->count();
        $rejected = $allRequests->where('status', 'rejected')->count();
        return view('apd-requests.create', compact(
            'total',
            'pending',
            'delivery',
            'approved',
            'rejected'
        ));
    }

    // User: store new request
    public function store(Request $request)
    {
        // Prevent admin from creating APD requests
        if (auth()->user()->role === 'admin') {
            return redirect()->route('apd-requests.admin-index')
                ->with('error', 'Admin tidak dapat membuat pengajuan APD.');
        }

        $data = $request->validate([
            'team_mandor'   => 'required|string|max:255',
            'helm'          => 'required|integer|min:0',
            'rompi'         => 'required|integer|min:0',
            'apboots'       => 'required|integer|min:0',
            'body_harness'  => 'required|integer|min:0',
            'sarung_tangan' => 'required|integer|min:0',
            'nama_cluster'  => 'required|string|max:255',
            'lokasi_project'=> 'nullable|string|max:255',
        ]);
        // Compute total APD count automatically
        $data['jumlah_apd'] = array_sum([
            $data['helm'],
            $data['rompi'],
            $data['apboots'],
            $data['body_harness'],
            $data['sarung_tangan'],
        ]);

        $data['user_id'] = auth()->id();
        // Compute and include structured sequential nomor_pengajuan before insertion
        $data['nomor_pengajuan'] = PengajuanNumberService::next('APD', date('Ym'));
        // Create APD request with nomor_pengajuan
        $apdRequest = ApdRequest::create($data);
        // Notify all admins of new APD request
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new NewApdRequestNotification($apdRequest));
        return redirect()->route('apd-requests.index')->with('success', 'Pengajuan APD berhasil dibuat.');
    }

    // User: detail view
    public function show(ApdRequest $apdRequest)
    {
        // Allow the owner or admins to view the detail page. Avoid relying on an undefined policy.
        $user = auth()->user();
        if ($user->id !== $apdRequest->user_id && !$user->can('kelola-akun')) {
            abort(403);
        }
        return view('apd-requests.show', compact('apdRequest'));
    }

    // User: edit form (only for pending or rejected requests)
    public function edit(ApdRequest $apdRequest)
    {
        // Allow editing only for owner and only if pending or rejected
        if ($apdRequest->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit pengajuan ini.');
        }

        if (!in_array($apdRequest->status, ['pending', 'rejected'])) {
            return redirect()->route('apd-requests.show', $apdRequest)
                           ->with('error', 'Pengajuan APD tidak dapat diedit karena sudah diproses.');
        }

        return view('apd-requests.edit', compact('apdRequest'));
    }

    // User: update APD request (only for pending or rejected requests)
    public function updateUser(Request $request, ApdRequest $apdRequest)
    {
        // Allow updating only for owner and only if pending or rejected
        if ($apdRequest->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit pengajuan ini.');
        }

        if (!in_array($apdRequest->status, ['pending', 'rejected'])) {
            return redirect()->route('apd-requests.show', $apdRequest)
                           ->with('error', 'Pengajuan APD tidak dapat diedit karena sudah diproses.');
        }

        $data = $request->validate([
            'team_mandor' => 'required|string|max:255',
            'nama_cluster' => 'required|string|max:255',
            'helm' => 'required|integer|min:0|max:100',
            'rompi' => 'required|integer|min:0|max:100',
            'apboots' => 'required|integer|min:0|max:100',
            'body_harness' => 'required|integer|min:0|max:100',
            'sarung_tangan' => 'required|integer|min:0|max:100',
        ]);

        // Compute total APD count automatically
        $data['jumlah_apd'] = array_sum([
            $data['helm'],
            $data['rompi'],
            $data['apboots'],
            $data['body_harness'],
            $data['sarung_tangan'],
        ]);

        // If request was rejected, reset to pending status
        if ($apdRequest->status === 'rejected') {
            $data['status'] = 'pending';
            $data['approved_at'] = null;
        }

        $apdRequest->update($data);

        return redirect()->route('apd-requests.show', $apdRequest)
                        ->with('success', 'Pengajuan APD berhasil diperbarui.');
    }

    // User: history of approved/rejected requests
    public function history(Request $request)
    {
        // All user requests for summary counts
        $allRequests = auth()->user()->apdRequests()->get();
        // Build query for requests list
        $query = auth()->user()->apdRequests()->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: show received and rejected history
            $query->whereIn('status', ['received', 'rejected']);
        }
        $requests = $query->get();
        return view('apd-requests.history', compact('requests', 'allRequests'));
    }

    // Admin: history of all approved/rejected requests
    public function adminHistory(Request $request)
    {
    // Show requests that have been received (user confirmation) or rejected
    $query = ApdRequest::whereIn('status', ['received', 'rejected'])->latest();

        // Apply cluster filter if provided
        if ($request->filled('cluster')) {
            $query->where('nama_cluster', $request->cluster);
        }

        // Apply status filter if provided
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Apply search filter for PIC name
        if ($request->filled('search')) {
            $query->where('nama_pic', 'LIKE', '%' . $request->search . '%');
        }

        $requests = $query->get();
        $statusFilter = $request->filled('status') ? $request->status : 'all';
        $search = $request->input('search');

        return view('apd-requests.admin-history', compact('requests', 'statusFilter', 'search'));
    }

    // Admin: list all for approval
    public function indexAdmin(Request $request)
    {
        $query = ApdRequest::latest();

        // Default to pending status if no status filter is specified
        $statusFilter = $request->filled('status') ? $request->status : 'pending';

        // Apply status filter
        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        // Apply cluster filter
        if ($request->filled('cluster')) {
            $query->where('nama_cluster', $request->cluster);
        }

        $requests = $query->get();

        // Get all requests for statistics (regardless of filter)
        $allRequests = ApdRequest::all();
        // Fetch or create stock items and prepare summary (allow going negative)
        $itemNames = ['helm', 'rompi', 'apboots', 'body_harness', 'sarung_tangan'];
        $stockItems = [];
        foreach ($itemNames as $name) {
            $stock = StockItem::firstOrCreate(['name' => $name], ['stock' => 50]);
            $stockItems[$name] = $stock->stock;
        }
        return view('apd-requests.admin-index', compact('requests', 'allRequests', 'statusFilter', 'stockItems'));
    }

    // Admin: approve request
    public function approve(ApdRequest $apdRequest)
    {
        Log::info('ApdRequestController@approve called', ['apd_request_id' => $apdRequest->id, 'user_id' => auth()->id()]);
        // Deduct stock items (allow negative stock when request exceeds current stock)
        foreach (['helm', 'rompi', 'apboots', 'body_harness', 'sarung_tangan'] as $item) {
            $stock = StockItem::firstOrCreate(['name' => $item], ['stock' => 50]);
            $stock->decrement('stock', $apdRequest->$item);
        }
        // Update request status to delivery when admin sends APD
        $apdRequest->update([
            'status' => 'delivery',
            'approved_at' => now(),
        ]);
        // Notify user of delivery
        $apdRequest->user->notify(new ApdRequestStatusNotification($apdRequest, 'delivery'));
        // Redirect to admin index filtered by delivery so admin sees the delivered item immediately
        return redirect()->route('apd-requests.admin-index', ['status' => 'delivery'])
            ->with('success', 'Pengajuan APD disetujui dan stock telah dikurangi.');
    }

    // User: confirm receipt of APD
    public function receive(ApdRequest $apdRequest)
    {
        if ($apdRequest->status !== 'delivery') {
            return back()->with('error', 'APD belum dikirim atau sudah diterima.');
        }
        $apdRequest->update([
            'status' => 'received',
            'approved_at' => now(),
        ]);
        // Notify user of receipt confirmation
        $apdRequest->user->notify(new ApdRequestStatusNotification($apdRequest, 'received'));
        // Redirect to history after receiving
        return redirect()->route('apd-requests.history')->with('success', 'APD berhasil diterima.');
    }
    public function reject(ApdRequest $apdRequest)
    {
        $apdRequest->update([
            'status' => 'rejected',
            'approved_at' => null,
        ]);
        // Notify user of rejection
        $apdRequest->user->notify(new ApdRequestStatusNotification($apdRequest, 'rejected'));
        return back()->with('success', 'Pengajuan APD berhasil ditolak.');
    }

    /**
     * Admin: send/deliver APD with actual quantities
     */
    public function update(Request $request, ApdRequest $apdRequest)
    {
        // Ensure only admins
        $this->authorize('kelola-akun');

        // Validate delivered quantities
        $data = $request->validate([
            'helm'          => 'required|integer|min:0',
            'rompi'         => 'required|integer|min:0',
            'apboots'       => 'required|integer|min:0',
            'body_harness'  => 'required|integer|min:0',
            'sarung_tangan' => 'required|integer|min:0',
        ]);
        // Adjust stock: return reserved (approved) quantities, then deduct actual delivered
        foreach (['helm', 'rompi', 'apboots', 'body_harness', 'sarung_tangan'] as $item) {
            $stock = StockItem::firstOrCreate(['name' => $item], ['stock' => 50]);
            // return previously reserved stock
            $stock->increment('stock', $apdRequest->$item);
            // deduct actual delivered quantity
            $stock->decrement('stock', $data[$item]);
        }
        // Compute total delivered
        $data['jumlah_apd'] = array_sum(array_values($data));
        $data['status'] = 'delivery';
        $data['approved_at'] = now();
        $apdRequest->update($data);

        return back()->with('success', 'APD berhasil dikirim.');
    }

    // Admin: bulk delete history requests
    public function adminHistoryDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->route('apd-requests.admin-history')->with('error', 'Tidak ada riwayat yang dipilih.');
        }
        ApdRequest::whereIn('id', $ids)->delete();
        return redirect()->route('apd-requests.admin-history')->with('success', 'Riwayat pengajuan APD berhasil dihapus.');
    }

    // Admin: restock items from a previously approved APD
    public function restock(ApdRequest $apdRequest)
    {
        $this->authorize('kelola-akun');
        foreach (['helm', 'rompi', 'apboots', 'body_harness', 'sarung_tangan'] as $item) {
            $stock = StockItem::firstOrCreate(['name' => $item], ['stock' => 50]);
            $stock->increment('stock', $apdRequest->$item);
        }
        // Tandai sudah direstock
        $apdRequest->update(['restocked' => true]);
        // Redirect kembali ke daftar APD Dikirim agar filter status tetap aktif
        return redirect()->route('apd-requests.admin-index', ['status' => 'delivery'])
            ->with('success', 'Stock berhasil dikembalikan.');
    }

    /**
     * Admin: reset semua stok APD ke default 50
     */
    public function resetStocks()
    {
        $this->authorize('kelola-akun');
        // Set all stock items back to 50
        StockItem::query()->update(['stock' => 50]);
        return back()->with('success', 'Semua stok APD direset ke 50.');
    }

    /**
     * Export all activities to CSV
     */
    public function exportAllActivitiesCsv(Request $request)
    {
        $query = ApdRequest::latest();

        // Apply status filter
        $statusFilter = $request->filled('status') ? $request->status : 'all';
        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        // Apply cluster filter
        if ($request->filled('cluster')) {
            $query->where('nama_cluster', $request->cluster);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $query->where('nama_pic', 'LIKE', '%' . $request->search . '%');
        }

        $requests = $query->get();

        $format = $request->get('format', 'xlsx');

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="apd_all_activities_' . date('Y-m-d_H-i-s') . '.csv"',
            ];

            $callback = function() use ($requests) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, [
                    'Nama Cluster','Nama PIC','Alamat','Helm','Rompi','AP Boots','Body Harness','Sarung Tangan','Total APD','Status','Tanggal Pengajuan','Tanggal Approved'
                ], ';');

                foreach ($requests as $r) {
                    fputcsv($file, [
                        $r->nama_cluster ?? '-',
                        $r->user ? $r->user->name : '-',
                        $r->lokasi_project ?? '-',
                        $r->helm ?? 0,
                        $r->rompi ?? 0,
                        $r->apboots ?? 0,
                        $r->body_harness ?? 0,
                        $r->sarung_tangan ?? 0,
                        $r->jumlah_apd ?? 0,
                        ucfirst($r->status ?? '-'),
                        $r->created_at ? $r->created_at->format('d/m/Y H:i') : '-',
                        $r->approved_at ? $r->approved_at->format('d/m/Y H:i') : '-',
                    ], ';');
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Default to XLSX using ApdRequestsExport
        $fileName = 'apd_all_activities_' . date('Y-m-d_H-i-s') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ApdRequestsExport($requests), $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * Export admin history to CSV
     */
    public function exportAdminHistoryCsv(Request $request)
    {
        $query = ApdRequest::whereIn('status', ['received', 'rejected'])->latest();

        // Apply same filters as adminHistory method
        if ($request->filled('cluster')) {
            $query->where('nama_cluster', $request->cluster);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('nama_pic', 'LIKE', '%' . $request->search . '%');
        }

        $requests = $query->get();

        $format = $request->get('format', 'xlsx');

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="apd_admin_history_' . date('Y-m-d_H-i-s') . '.csv"',
            ];

            $callback = function() use ($requests) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, [
                    'Nama Cluster','Nama PIC','Alamat','Helm','Rompi','AP Boots','Body Harness','Sarung Tangan','Total APD','Status','Tanggal Pengajuan','Tanggal Approved'
                ], ';');

                foreach ($requests as $r) {
                    fputcsv($file, [
                        $r->nama_cluster ?? '-',
                        $r->user ? $r->user->name : '-',
                        $r->lokasi_project ?? '-',
                        $r->helm ?? 0,
                        $r->rompi ?? 0,
                        $r->apboots ?? 0,
                        $r->body_harness ?? 0,
                        $r->sarung_tangan ?? 0,
                        $r->jumlah_apd ?? 0,
                        ucfirst($r->status ?? '-'),
                        $r->created_at ? $r->created_at->format('d/m/Y H:i') : '-',
                        $r->approved_at ? $r->approved_at->format('d/m/Y H:i') : '-',
                    ], ';');
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Default to XLSX using ApdRequestsExport
        $fileName = 'apd_admin_history_' . date('Y-m-d_H-i-s') . '.xlsx';
    return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ApdRequestsExport($requests), $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * Export single APD request detail to CSV
     */
    public function exportDetailCsv(ApdRequest $apdRequest)
    {
        // Allow owner or admin to export
        $user = auth()->user();
        if ($user->id !== $apdRequest->user_id && !$user->can('kelola-akun')) {
            abort(403);
        }

        $format = request()->get('format', 'xlsx');

        if ($format === 'csv') {
            $filename = 'apd_detail_' . $apdRequest->nomor_pengajuan . '_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($apdRequest) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($file, [
                    'Nomor Pengajuan','Status','Nama Tim Mandor','Nama Cluster','PIC Pengajuan','Helm (pcs)','Rompi (pcs)','AP Boots (pcs)','Body Harness (pcs)','Sarung Tangan (pcs)','Total APD (pcs)','Tanggal Pengajuan','Tanggal Disetujui'
                ], ';');

                fputcsv($file, [
                    $apdRequest->nomor_pengajuan,
                    ucfirst($apdRequest->status),
                    $apdRequest->team_mandor,
                    $apdRequest->nama_cluster,
                    $apdRequest->user->name,
                    $apdRequest->helm,
                    $apdRequest->rompi,
                    $apdRequest->apboots,
                    $apdRequest->body_harness,
                    $apdRequest->sarung_tangan,
                    $apdRequest->jumlah_apd,
                    $apdRequest->created_at->format('d/m/Y H:i'),
                    $apdRequest->approved_at ? $apdRequest->approved_at->format('d/m/Y H:i') : '-'
                ], ';');

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Default to XLSX
        $fileName = 'apd_detail_' . $apdRequest->nomor_pengajuan . '_' . date('Y-m-d') . '.xlsx';
    return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ApdRequestsExport(collect([$apdRequest])), $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }

    // Bulk delete selected APD requests
    public function bulkDelete(Request $request)
    {
        $this->authorize('kelola-akun');
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            ApdRequest::whereIn('id', $ids)->delete();
        }
        return redirect()->route('apd-requests.admin-index', ['status' => 'all'])
                         ->with('success', 'Pengajuan APD terpilih berhasil dihapus.');
    }
}
