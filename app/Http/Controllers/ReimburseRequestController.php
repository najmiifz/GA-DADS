<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use App\Models\ReimburseRequest;
use App\Services\PengajuanNumberService;

class ReimburseRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:kelola-akun')->only(['indexAdmin', 'approve', 'reject']);
    }

    // User: list own reimburse requests
    public function index()
    {
        if (!Schema::hasTable('reimburse_requests')) {
            $requests = collect();
        } else {
            // Only show pending reimburse requests in index
            $requests = auth()->user()->reimburseRequests()
                ->where('status', 'pending')
                ->latest()
                ->get();
        }
    // Also pass user's assets for inline reimbursement form
    $assets = auth()->user()->assets;
    return view('reimburse-requests.index', compact('requests', 'assets'));
    }

    // User: show create form
    public function create()
    {
        // Prevent admin from creating reimburse requests
        if (auth()->user()->role === 'admin') {
            return redirect()->route('reimburse-requests.admin-index')
                ->with('error', 'Admin tidak dapat membuat pengajuan reimburse. Silakan gunakan menu Admin Reimburse untuk mengelola pengajuan.');
        }

        // Fetch only motor assets for this user
        // Fetch motor assets owned by current user
        $assets = auth()->user()->assets()
            ->whereRaw('LOWER(jenis_aset) = ?', ['motor'])
            ->get();
        return view('reimburse-requests.create', compact('assets'));
    }

    // User: store new request
    public function store(Request $request)
    {
        // Prevent admin from creating reimburse requests
        if (auth()->user()->role === 'admin') {
            return redirect()->route('reimburse-requests.admin-index')
                ->with('error', 'Admin tidak dapat membuat pengajuan reimburse.');
        }

        $data = $request->validate([
            'biaya' => 'required|integer|min:0',
            'keterangan' => 'required|string',
            'tanggal_service' => 'required|date',
            'bukti_struk.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_bukti_service.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);
    $data['user_id'] = auth()->id();
    $data['asset_id'] = $request->input('asset_id');

        // Handle uploads first so we store paths into $data before creating the record
        if($request->hasFile('bukti_struk')) {
            $files = $request->file('bukti_struk');
            $stored = [];
            foreach($files as $file) {
                $stored[] = $file->store('reimburse', 'public');
            }
            // save as JSON array if multiple files
            $data['bukti_struk'] = json_encode($stored);
        }

        // handle optional service evidence photos (separate from struk)
        if($request->hasFile('foto_bukti_service')) {
            $evidenceFiles = $request->file('foto_bukti_service');
            $storedEvidence = [];
            foreach($evidenceFiles as $file) {
                $storedEvidence[] = $file->store('reimburse/evidence', 'public');
            }
            $data['foto_bukti_service'] = json_encode($storedEvidence);
        }

        // Generate nomor_pengajuan before creating the record
        $data['nomor_pengajuan'] = PengajuanNumberService::next('RBM', date('Ym'));

        // Create the reimburse record
        $reimburse = ReimburseRequest::create($data);
        return redirect()->route('reimburse-requests.index')->with('success', 'Pengajuan reimburse berhasil dibuat.');
    }

    // User: detail view
    public function show(ReimburseRequest $reimburseRequest)
    {
    // Allow user to view detail (authorization handled in controller or via policy)
    // Removed explicit authorization to prevent unintended 403 for users.
    return view('reimburse-requests.show', compact('reimburseRequest'));
    }

    // User: edit rejected reimburse request
    public function edit(ReimburseRequest $reimburseRequest)
    {
        // Only allow editing by the request owner
        if ($reimburseRequest->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit pengajuan ini.');
        }

        // Only allow editing of pending or rejected requests
        if (!in_array($reimburseRequest->status, ['pending', 'rejected'])) {
            return redirect()->route('reimburse-requests.show', $reimburseRequest)
                ->with('error', 'Pengajuan yang sudah disetujui atau dalam proses lain tidak dapat diedit.');
        }

        // Fetch motor assets owned by current user
        $assets = auth()->user()->assets()
            ->whereRaw('LOWER(jenis_aset) = ?', ['motor'])
            ->get();

        return view('reimburse-requests.edit', compact('reimburseRequest', 'assets'));
    }

    // User: update rejected reimburse request
    public function updateUser(Request $request, ReimburseRequest $reimburseRequest)
    {
        // Only allow updating by the request owner
        if ($reimburseRequest->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate pengajuan ini.');
        }

        // Only allow updating of pending or rejected requests
        if (!in_array($reimburseRequest->status, ['pending', 'rejected'])) {
            return redirect()->route('reimburse-requests.show', $reimburseRequest)
                ->with('error', 'Pengajuan yang sudah disetujui atau dalam proses lain tidak dapat diedit.');
        }

        $data = $request->validate([
            'biaya' => 'required|integer|min:0',
            'keterangan' => 'required|string',
            'tanggal_service' => 'required|date',
            'bukti_struk.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_bukti_service.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $data['asset_id'] = $request->input('asset_id');

        // If request was rejected and now being updated, reset to pending
        if ($reimburseRequest->status === 'rejected') {
            $data['status'] = 'pending';
            $data['catatan_admin'] = null;
            $data['approved_by'] = null;
            $data['approved_at'] = null;
        }

        // Handle receipt files
        if($request->hasFile('bukti_struk')) {
            $files = $request->file('bukti_struk');
            $stored = [];
            foreach($files as $file) {
                $stored[] = $file->store('reimburse', 'public');
            }
            $data['bukti_struk'] = json_encode($stored);
        }

        // Handle service evidence photos
        if($request->hasFile('foto_bukti_service')) {
            $evidenceFiles = $request->file('foto_bukti_service');
            $storedEvidence = [];
            foreach($evidenceFiles as $file) {
                $storedEvidence[] = $file->store('reimburse/evidence', 'public');
            }
            $data['foto_bukti_service'] = json_encode($storedEvidence);
        }

        $reimburseRequest->update($data);

        $message = $reimburseRequest->wasChanged('status')
            ? 'Pengajuan reimburse berhasil diperbarui dan status dikembalikan ke pending untuk ditinjau ulang.'
            : 'Pengajuan reimburse berhasil diperbarui.';

        return redirect()->route('reimburse-requests.show', $reimburseRequest)
            ->with('success', $message);
    }

    // User: history of approved/rejected requests
    public function history()
    {
        if (!Schema::hasTable('reimburse_requests')) {
            $requests = collect();
        } else {
            if (auth()->user()->role === 'admin') {
                // Admin: show all approved/rejected reimburse requests
                $requests = ReimburseRequest::whereIn('status', ['approved', 'rejected'])
                    ->latest()
                    ->get();
            } else {
                // User: show own history
                $requests = auth()->user()->reimburseRequests()
                    ->whereIn('status', ['approved', 'rejected'])
                    ->latest()
                    ->get();
            }
        }
        return view('reimburse-requests.history', compact('requests'));
    }

    // Admin: list all
    public function indexAdmin(Request $request)
    {
        if (!Schema::hasTable('reimburse_requests')) {
            $requests = collect();
            $monthlyData = [];
        } else {
            $query = ReimburseRequest::with('user')->latest();

            // Apply status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Apply month filter
            if ($request->filled('month')) {
                $query->whereMonth('created_at', $request->month);
            }

            // Apply minimum amount filter
            if ($request->filled('min_amount')) {
                $query->where('biaya', '>=', $request->min_amount);
            }

            // Apply search filter for user name
            if ($request->filled('search')) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('nik', 'LIKE', '%' . $request->search . '%');
                });
            }

            $requests = $query->get();
            $search = $request->input('search');
            // Generate monthly totals for chart (all statuses)
            // Use SQLite strftime or MySQL DATE_FORMAT
            $driver = \DB::getDriverName();
            if ($driver === 'sqlite') {
                $monthlyData = ReimburseRequest::selectRaw("strftime('%Y-%m', created_at) as month, SUM(biaya) as total")
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('total', 'month')
                    ->toArray();
            } else {
                $monthlyData = ReimburseRequest::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(biaya) as total")
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('total', 'month')
                    ->toArray();
            }
        }
        return view('reimburse-requests.admin-index', compact('requests', 'monthlyData', 'search'));
    }

    // Admin: approve
    public function approve(ReimburseRequest $reimburseRequest)
    {
        $reimburseRequest->update(['status' => 'approved', 'approved_at' => now()]);
        return back()->with('success', 'Reimburse disetujui.');
    }

    // Admin: reject
    public function reject(ReimburseRequest $reimburseRequest)
    {
        $reimburseRequest->update(['status' => 'rejected']);
        return back()->with('error', 'Reimburse ditolak.');
    }

    /**
     * Export admin index to CSV
     */
    public function exportAdminIndexCsv(Request $request)
    {
        $query = ReimburseRequest::with(['user']);

        // Apply same filters as indexAdmin method
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }
        if ($request->filled('min_amount')) {
            $query->where('biaya', '>=', $request->min_amount);
        }
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('nik', 'LIKE', '%' . $request->search . '%');
            });
        }

        $requests = $query->get();

        $format = $request->get('format', 'xlsx');

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="reimburse_admin_' . date('Y-m-d_H-i-s') . '.csv"',
            ];

            $callback = function() use ($requests) {
                $file = fopen('php://output', 'w');

                // Add BOM for proper UTF-8 encoding in Excel
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                // CSV headers
                fputcsv($file, [
                    'No. Pengajuan', 'User', 'Asset', 'Jenis Reimburse', 'Biaya', 'Status', 'Tanggal Pengajuan', 'Tanggal Approved', 'Keterangan'
                ], ';');

                foreach ($requests as $req) {
                    fputcsv($file, [
                        $req->nomor_pengajuan ?: '-',
                        $req->user ? $req->user->name : '-',
                        $req->asset ? ($req->asset->merk . ' ' . $req->asset->tipe) : '-',
                        $req->jenis_reimburse ?: '-',
                        'Rp ' . number_format($req->biaya ?: 0, 0, ',', '.'),
                        $req->status,
                        $req->created_at->format('d/m/Y H:i'),
                        $req->approved_at ? $req->approved_at->format('d/m/Y H:i') : '-',
                        $req->keterangan ?: '-'
                    ], ';');
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Default: use styled XLSX via ReimburseRequestsExport
        $fileName = 'reimburse_admin_' . date('Y-m-d_H-i-s') . '.xlsx';
    return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ReimburseRequestsExport($requests), $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * Bulk delete reimburse requests
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->route('reimburse-requests.admin-index')->with('error', 'Tidak ada data yang dipilih.');
        }

        ReimburseRequest::whereIn('id', $ids)->delete();
        return redirect()->route('reimburse-requests.admin-index')->with('success', count($ids) . ' reimburse request berhasil dihapus.');
    }

    /**
     * Export single reimburse request detail to CSV
     */
    public function exportDetailCsv(ReimburseRequest $reimburseRequest)
    {
        // Check policy for viewing the request
        if (auth()->user()->role !== 'admin' && $reimburseRequest->user_id !== auth()->id()) {
            abort(403);
        }

        $format = request()->get('format', 'xlsx');

        if ($format === 'csv') {
            $filename = 'reimburse_detail_' . $reimburseRequest->nomor_pengajuan . '_' . date('Y-m-d') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($reimburseRequest) {
                $file = fopen('php://output', 'w');

                // Add BOM for UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                // Header row with all columns
                fputcsv($file, [
                    'Nomor Pengajuan','Status','PIC Pengajuan','Asset','No Seri','Jenis Reimburse','Biaya','Tanggal Service','Keterangan','Catatan Admin','Tanggal Pengajuan','Tanggal Disetujui'
                ], ';');

                // Data row
                fputcsv($file, [
                    $reimburseRequest->nomor_pengajuan,
                    ucfirst($reimburseRequest->status),
                    $reimburseRequest->user->name,
                    ($reimburseRequest->asset ? $reimburseRequest->asset->merk . ' (' . $reimburseRequest->asset->tipe . ')' : 'N/A'),
                    $reimburseRequest->asset->no_seri ?? '-',
                    $reimburseRequest->jenis_reimburse ?? '-',
                    'Rp ' . number_format($reimburseRequest->biaya, 0, ',', '.'),
                    $reimburseRequest->tanggal_service ? \Carbon\Carbon::parse($reimburseRequest->tanggal_service)->format('d/m/Y') : '-',
                    $reimburseRequest->keterangan,
                    $reimburseRequest->catatan_admin ?? '-',
                    $reimburseRequest->created_at->format('d/m/Y H:i'),
                    $reimburseRequest->approved_at ? $reimburseRequest->approved_at->format('d/m/Y H:i') : '-'
                ], ';');

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Default to XLSX detail export
        $fileName = 'reimburse_detail_' . $reimburseRequest->nomor_pengajuan . '_' . date('Y-m-d') . '.xlsx';
        // Use the ReimburseRequestsExport with a single-element collection for consistent headings/styling
    return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ReimburseRequestsExport(collect([$reimburseRequest])), $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }
}
