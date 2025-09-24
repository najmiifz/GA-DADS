<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SpjRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\PengajuanNumberService;

class SpjRequestController extends Controller
{
    public function userIndex(Request $request)
    {
        // User view: list their own SPJ requests, filter by status if provided
        $status = $request->get('status', 'all');
        $query = Auth::user()->spjRequests()->with('user');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $requests = $query->latest()->paginate(10);
        $allRequests = Auth::user()->spjRequests;

        return view('spj-requests.index', compact('requests', 'allRequests', 'status'));
    }

    public function index(Request $request)
    {
        // Admin view: list SPJ requests, filter by status if provided
        $query = SpjRequest::with('user');
        if ($request->get('status') === 'pending') {
            $query->where('status', 'pending');
        }
        $requests = $query->latest()->paginate(10);
        return view('spj.index', compact('requests'));
    }

    public function create()
    {
        // User SPJ form
        return view('spj.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bast_mutasi' => 'nullable|string',
            'bast_mutasi_file' => 'nullable|file|max:10240',
            'bast_inventaris' => 'nullable|string',
            'bast_inventaris_file' => 'nullable|file|max:10240',
            'nama_pegawai' => 'required|string',
            'keperluan' => 'required|string',
            'lokasi_project' => 'nullable|string',
            'penugasan_by' => 'required|string',
            'bukti_penugasan_file' => 'nullable|file|max:10240',
            'perjalanan_from' => 'required|string',
            'perjalanan_to' => 'required|string',
            'spj_date' => 'required|date',
            'transportasi' => 'required|string',
            'biaya_estimasi' => 'required|string',
            'nota_files.*' => 'nullable|file|max:10240'
        ]);
    $data['user_id'] = Auth::id();
    $data['nomor_pengajuan'] = PengajuanNumberService::next('SPJ', date('Ym'));
    // Combine travel route into single field
    $data['perjalanan_from_to'] = $data['perjalanan_from'].' - '.$data['perjalanan_to'];

        // Handle single files
        if ($request->hasFile('bast_mutasi_file')) {
            $data['bast_mutasi_file'] = $request->file('bast_mutasi_file')->store('spj', 'public');
        }
        if ($request->hasFile('bast_inventaris_file')) {
            $data['bast_inventaris_file'] = $request->file('bast_inventaris_file')->store('spj', 'public');
        }
        if ($request->hasFile('bukti_penugasan_file')) {
            $data['bukti_penugasan_file'] = $request->file('bukti_penugasan_file')->store('spj', 'public');
        }
        // Handle nota files
        if ($request->hasFile('nota_files')) {
            $paths = [];
            foreach ($request->file('nota_files') as $file) {
                $paths[] = $file->store('spj/nota', 'public');
            }
            $data['nota_files'] = $paths;
        }

    $spj = SpjRequest::create($data);

    return redirect()->route('dashboard')->with('success', 'Pengajuan SPJ berhasil dikirim.');
    }

    public function show(SpjRequest $spjRequest)
    {
        // Admin detail view
        return view('spj.show', compact('spjRequest'));
    }

    /**
     * User-facing detail view: allow owner or admin to view a SPJ request.
     */
    public function userShow(SpjRequest $spjRequest)
    {
        $user = Auth::user();
        \Log::info('SPJ userShow called', [
            'spj_id' => $spjRequest->id,
            'spj_user_id' => $spjRequest->user_id,
            'current_user_id' => $user->id ?? null,
            'current_user_role' => $user->role ?? null
        ]);

        // Allow if current user is the owner or is admin
        if ($user && ($user->id === $spjRequest->user_id || in_array($user->role, ['admin', 'super-admin']))) {
            return view('spj.show', compact('spjRequest'));
        }

        abort(403);
    }

    /**
     * User: edit rejected SPJ request
     */
    public function edit(SpjRequest $spjRequest)
    {
        // Only allow editing by the request owner
        if ($spjRequest->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit pengajuan ini.');
        }

        // Only allow editing of pending or rejected requests
        if (!in_array($spjRequest->status, ['pending', 'rejected'])) {
            return redirect()->route('spj.user-show', $spjRequest)
                ->with('error', 'Pengajuan yang sudah disetujui atau dalam proses lain tidak dapat diedit.');
        }

        return view('spj.edit', compact('spjRequest'));
    }

    /**
     * User: update rejected SPJ request
     */
    public function updateUser(Request $request, SpjRequest $spjRequest)
    {
        // Only allow updating by the request owner
        if ($spjRequest->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate pengajuan ini.');
        }

        // Only allow updating of pending or rejected requests
        if (!in_array($spjRequest->status, ['pending', 'rejected'])) {
            return redirect()->route('spj.user-show', $spjRequest)
                ->with('error', 'Pengajuan yang sudah disetujui atau dalam proses lain tidak dapat diedit.');
        }

        $data = $request->validate([
            'perjalanan_from' => 'required|string',
            'perjalanan_to' => 'required|string',
            'perjalanan_date' => 'required|date',
            'spj_date' => 'required|date',
            'bast_mutasi_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'bast_inventaris_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'bukti_penugasan_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'nota_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // If request was rejected and now being updated, reset to pending
        if ($spjRequest->status === 'rejected') {
            $data['status'] = 'pending';
            $data['catatan_admin'] = null;
            $data['approved_by'] = null;
            $data['approved_at'] = null;
        }

        // Handle file uploads
        if ($request->hasFile('bast_mutasi_file')) {
            $data['bast_mutasi_file'] = $request->file('bast_mutasi_file')->store('spj', 'public');
        }
        if ($request->hasFile('bast_inventaris_file')) {
            $data['bast_inventaris_file'] = $request->file('bast_inventaris_file')->store('spj', 'public');
        }
        if ($request->hasFile('bukti_penugasan_file')) {
            $data['bukti_penugasan_file'] = $request->file('bukti_penugasan_file')->store('spj', 'public');
        }
        if ($request->hasFile('nota_files')) {
            $paths = [];
            foreach ($request->file('nota_files') as $file) {
                $paths[] = $file->store('spj/nota', 'public');
            }
            $data['nota_files'] = json_encode($paths);
        }

        $spjRequest->update($data);

        $message = $spjRequest->wasChanged('status')
            ? 'Pengajuan SPJ berhasil diperbarui dan status dikembalikan ke pending untuk ditinjau ulang.'
            : 'Pengajuan SPJ berhasil diperbarui.';

        return redirect()->route('spj.user-show', $spjRequest)
            ->with('success', $message);
    }

    /**
     * Approve SPJ request (admin only).
     */
    public function approve(SpjRequest $spjRequest)
    {
        $spjRequest->update(['status' => 'approved']);

        // If perjalanan_to is present, update the owner's lokasi accordingly.
        try {
            $perjalananTo = $spjRequest->perjalanan_to ?? null;
            if (!empty($perjalananTo) && $spjRequest->user) {
                $user = $spjRequest->user;
                $user->lokasi = $perjalananTo;
                $user->save();
                // User model has booted observer to sync lokasi to assigned assets
            }
        } catch (\Exception $e) {
            // swallow any exception to avoid breaking approval flow; log if desired
            // logger()->error('Failed to update user lokasi on SPJ approve: '.$e->getMessage());
        }

        return redirect()->route('spj.index')->with('success', 'SPJ request approved');
    }

    /**
     * Reject SPJ request (admin only).
     */
    public function reject(SpjRequest $spjRequest)
    {
        $spjRequest->update(['status' => 'rejected']);
        return redirect()->route('spj.index')->with('error', 'SPJ request rejected');
    }

    /**
     * SPJ history view (admin only).
     */
    public function history()
    {
        $requests = SpjRequest::with('user')
                     ->whereIn('status', ['approved', 'rejected'])
                     ->latest()
                     ->paginate(10);
        return view('spj.history', compact('requests'));
    }
    // User: export SPJ detail as CSV
    public function exportCsv(SpjRequest $spjRequest)
    {
        // Only allow owner or admin
        $user = Auth::user();
        if (!($user->id === $spjRequest->user_id || in_array($user->role, ['admin', 'super-admin']))) {
            abort(403);
        }

        $format = request()->get('format', 'xlsx');

        if ($format === 'csv') {
            $filename = 'spj_' . $spjRequest->id . '_' . date('Y-m-d_His') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($spjRequest) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
                fputcsv($file, [
                    'ID','Nama Pegawai','SPJ Date','Status','Keperluan','BAST Mutasi','BAST Inventaris','Penugasan By','Perjalanan From','Perjalanan To','Transportasi','Biaya Estimasi'
                ], ';');

                $spjDate = $spjRequest->spj_date ? \Carbon\Carbon::parse($spjRequest->spj_date)->format('d/m/Y') : '-';
                fputcsv($file, [
                    $spjRequest->id,
                    $spjRequest->nama_pegawai ?? '-',
                    $spjDate,
                    ucfirst($spjRequest->status ?? '-'),
                    $spjRequest->keperluan ?? '-',
                    $spjRequest->bast_mutasi ?? '-',
                    $spjRequest->bast_inventaris ?? '-',
                    $spjRequest->penugasan_by ?? '-',
                    $spjRequest->perjalanan_from ?? '-',
                    $spjRequest->perjalanan_to ?? '-',
                    $spjRequest->transportasi ?? '-',
                    $spjRequest->biaya_estimasi ?? '-'
                ], ';');

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Default to XLSX using SpjDetailExport
        $fileName = 'spj_' . $spjRequest->id . '_' . date('Y-m-d_His') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SpjDetailExport($spjRequest), $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }
}
