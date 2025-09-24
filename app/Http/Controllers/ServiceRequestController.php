<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Asset;
use App\Models\ApdRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewServiceRequestNotification;
use App\Services\PengajuanNumberService;

class ServiceRequestController extends Controller
{
    public function index()
    {
    $serviceRequests = ServiceRequest::with(['asset', 'user', 'approver'])
                    // Hanya tampilkan yang pending atau rejected, yang sudah approved langsung pindah ke service_pending
                    ->whereIn('status', ['pending', 'rejected'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);

        return view('service-requests.index', compact('serviceRequests'));
    }

    public function create()
    {
        // Get only car assets for service requests
        $assets = auth()->user()->assets()->where('jenis_aset', 'Mobil')->get();
        return view('service-requests.create', compact('assets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'lokasi_project' => 'required|string|max:255',
            'km_saat_ini' => 'required|string|max:255',
            'keluhan' => 'required|string',
            'estimasi_harga' => 'required|numeric|min:0',
            'foto_estimasi' => 'required|array|min:1',
            'foto_estimasi.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'foto_km.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'tanggal_servis' => 'required|date',
            'foto_bukti_service' => 'nullable|array|max:3',
            'foto_bukti_service.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Upload foto KM
            $fotoKmFiles = [];
            if ($request->hasFile('foto_km')) {
                foreach ($request->file('foto_km') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('service-requests/km', $filename, 'public');
                    $fotoKmFiles[] = $filename;
                }
            }

            // Upload foto estimasi
            $fotoEstimasiFiles = [];
            if ($request->hasFile('foto_estimasi')) {
                foreach ($request->file('foto_estimasi') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('service-requests/estimates', $filename, 'public');
                    $fotoEstimasiFiles[] = $filename;
                }
            }

            // Upload foto bukti service
            $fotoBuktiServiceFiles = [];
            if ($request->hasFile('foto_bukti_service')) {
                foreach ($request->file('foto_bukti_service') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('service-requests/service-evidence', $filename, 'public');
                    $fotoBuktiServiceFiles[] = $filename;
                }
            }

            // Generate nomor_pengajuan first so the NOT NULL DB constraint is satisfied
            $nomor = PengajuanNumberService::next('SR', date('Ym'));

            $serviceRequest = ServiceRequest::create([
                'nomor_pengajuan' => $nomor,
                'lokasi_project' => $request->input('lokasi_project'),
                'asset_id' => $request->asset_id,
                'user_id' => Auth::id(),
                'km_saat_ini' => $request->km_saat_ini,
                'keluhan' => $request->keluhan,
                'estimasi_harga' => $request->estimasi_harga,
                'foto_estimasi' => $fotoEstimasiFiles,
                'foto_km' => $fotoKmFiles,
                'status' => 'pending',
                'tanggal_servis' => $request->input('tanggal_servis'),
                'foto_bukti_service' => $fotoBuktiServiceFiles
            ]);
            DB::commit();

                // Notify all admins about new service request
                $admins = User::where('role', 'admin')->get();
                Notification::send($admins, new NewServiceRequestNotification($serviceRequest));

            return redirect()->route('service-requests.show', $serviceRequest)
                           ->with('success', 'Pengajuan service berhasil dibuat dengan nomor: ' . $serviceRequest->nomor_pengajuan);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal membuat pengajuan service: ' . $e->getMessage());
        }
    }

    public function show(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load(['asset', 'user', 'approver']);
        return view('service-requests.show', compact('serviceRequest'));
    }
    /**
     * Return service request data as JSON for AJAX detail view.
     */
    public function showJson(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load(['asset', 'user', 'approver']);
        return response()->json(['serviceRequest' => $serviceRequest]);
    }

    public function edit(ServiceRequest $serviceRequest)
    {
        // Allow editing if pending or rejected
        if (!$serviceRequest->isPending() && !$serviceRequest->isRejected()) {
            return redirect()->route('service-requests.show', $serviceRequest)
                           ->with('error', 'Pengajuan service tidak dapat diedit karena sudah diproses.');
        }

        $assets = Asset::where('user_id', Auth::id())->where('jenis_aset', 'Mobil')->get();
        return view('service-requests.edit', compact('serviceRequest', 'assets'));
    }

    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        // Allow updating if pending or rejected
        if (!$serviceRequest->isPending() && !$serviceRequest->isRejected()) {
            return redirect()->route('service-requests.show', $serviceRequest)
                           ->with('error', 'Pengajuan service tidak dapat diedit karena sudah diproses.');
        }

    $request->validate([
            'asset_id' => 'required|exists:assets,id',
        'lokasi_project' => 'required|string|max:255',
            'km_saat_ini' => 'required|string|max:255',
            'keluhan' => 'required|string',
            'estimasi_harga' => 'required|numeric|min:0',
            'foto_estimasi.*' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            'foto_km.*' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        DB::beginTransaction();
        try {
            $fotoKmFiles = $serviceRequest->foto_km ?? [];
            $fotoEstimasiFiles = $serviceRequest->foto_estimasi ?? [];

            // Upload foto KM baru jika ada
            if ($request->hasFile('foto_km')) {
                foreach ($request->file('foto_km') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('service-requests/km', $filename, 'public');
                    $fotoKmFiles[] = $filename;
                }
            }

            // Upload foto estimasi baru jika ada
            if ($request->hasFile('foto_estimasi')) {
                foreach ($request->file('foto_estimasi') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('service-requests/estimates', $filename, 'public');
                    $fotoEstimasiFiles[] = $filename;
                }
            }

            // Prepare update data
            $updateData = [
                'lokasi_project' => $request->input('lokasi_project'),
                'asset_id' => $request->asset_id,
                'km_saat_ini' => $request->km_saat_ini,
                'keluhan' => $request->keluhan,
                'estimasi_harga' => $request->estimasi_harga,
                'foto_estimasi' => $fotoEstimasiFiles,
                'foto_km' => $fotoKmFiles
            ];

            // If request was rejected, reset to pending status and clear admin notes
            if ($serviceRequest->isRejected()) {
                $updateData['status'] = 'pending';
                $updateData['catatan_admin'] = null;
                $updateData['approved_by'] = null;
                $updateData['approved_at'] = null;
            }

            $serviceRequest->update($updateData);

            DB::commit();

            return redirect()->route('service-requests.show', $serviceRequest)
                           ->with('success', 'Pengajuan service berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal memperbarui pengajuan service: ' . $e->getMessage());
        }
    }

    public function destroy(ServiceRequest $serviceRequest)
    {
        if (!$serviceRequest->isPending()) {
            return redirect()->route('service-requests.index')
                           ->with('error', 'Pengajuan service tidak dapat dihapus karena sudah diproses.');
        }

        // Delete files
        if ($serviceRequest->foto_km) {
            foreach ($serviceRequest->foto_km as $filename) {
                Storage::disk('public')->delete('service-requests/km/' . $filename);
            }
        }

        if ($serviceRequest->foto_estimasi) {
            foreach ($serviceRequest->foto_estimasi as $filename) {
                Storage::disk('public')->delete('service-requests/estimates/' . $filename);
            }
        }

        if ($serviceRequest->foto_invoice) {
            foreach ($serviceRequest->foto_invoice as $filename) {
                Storage::disk('public')->delete('service-requests/invoices/' . $filename);
            }
        }

        // Log activity
        $serviceRequest->delete();
    }

    /**
     * Bulk delete selected service requests (only pending).
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->route('service-requests.all-activities')
                ->with('error', 'Tidak ada aktivitas yang dipilih untuk dihapus.');
        }
        $requests = ServiceRequest::whereIn('id', $ids)->get();
        $deletedCount = 0;
        foreach ($requests as $req) {
            if ($req->status === 'pending') {
                $req->delete();
                $deletedCount++;
            }
        }
        return redirect()->route('service-requests.all-activities')
            ->with('success', "Berhasil menghapus {$deletedCount} aktivitas.");
    }

    public function approve(Request $request, ServiceRequest $serviceRequest)
    {
        if (!$serviceRequest->canBeApproved()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Pengajuan service tidak dapat disetujui.'], 400);
            }
            return back()->with('error', 'Pengajuan service tidak dapat disetujui.');
        }

        $request->validate([
            'catatan_admin' => 'nullable|string'
        ]);

        $serviceRequest->update([
            'status' => 'service_pending',
            'catatan_admin' => $request->catatan_admin,
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Pengajuan service berhasil disetujui.']);
        }
        return back()->with('success', 'Pengajuan service berhasil disetujui.');
    }

    public function sendToServicePending(ServiceRequest $serviceRequest)
    {
        if (!$serviceRequest->canBeServicePending()) {
            return back()->with('error', 'Pengajuan service tidak dapat dikirim ke service pending.');
        }

        $serviceRequest->update([
            'status' => 'service_pending'
        ]);

        return back()->with('success', 'Pengajuan service telah dikirim ke user untuk melakukan service.');
    }

    public function reject(Request $request, ServiceRequest $serviceRequest)
    {
        if (!$serviceRequest->canBeRejected()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Pengajuan service tidak dapat ditolak.'], 400);
            }
            return back()->with('error', 'Pengajuan service tidak dapat ditolak.');
        }

        $request->validate([
            'reason' => 'required|string'
        ]);

        $serviceRequest->update([
            'status' => 'rejected',
            'catatan_admin' => $request->reason,
            'approved_by' => Auth::id(),
            'approved_at' => now()
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Pengajuan service berhasil ditolak.']);
        }
        return back()->with('success', 'Pengajuan service berhasil ditolak.');
    }

    public function complete(Request $request, ServiceRequest $serviceRequest)
    {
        if (!$serviceRequest->canBeCompleted()) {
            return back()->with('error', 'Pengajuan service tidak dapat diselesaikan.');
        }

        $request->validate([
            'biaya_servis' => 'required|numeric|min:0',
            'tanggal_servis' => 'required|date',
            'keterangan_servis' => 'required|string',
            'foto_invoice.*' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120'
        ]);

        DB::beginTransaction();
        try {
            // Upload foto invoice
            $fotoInvoiceFiles = [];
            if ($request->hasFile('foto_invoice')) {
                foreach ($request->file('foto_invoice') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('service-requests/invoices', $filename, 'public');
                    $fotoInvoiceFiles[] = $filename;
                }
            }

            $serviceRequest->update([
                'status' => 'completed',
                'biaya_servis' => $request->biaya_servis,
                'tanggal_servis' => $request->tanggal_servis,
                'keterangan_servis' => $request->keterangan_servis,
                'foto_invoice' => $fotoInvoiceFiles
            ]);

            // Create service history entry
            $serviceRequest->asset->services()->create([
                'service_date' => $request->tanggal_servis,
                'description' => $request->keterangan_servis,
                'cost' => $request->biaya_servis,
                'vendor' => 'Service Request - ' . $serviceRequest->nomor_pengajuan
            ]);

            DB::commit();

            return back()->with('success', 'Pengajuan service berhasil diselesaikan dan ditambahkan ke riwayat service.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal menyelesaikan pengajuan service: ' . $e->getMessage());
        }
    }

    public function deleteFotoKm(Request $request, ServiceRequest $serviceRequest)
    {
        if (!$serviceRequest->isPending()) {
            return response()->json(['error' => 'Tidak dapat menghapus foto'], 403);
        }

        $filename = $request->filename;
        $fotoKm = $serviceRequest->foto_km ?? [];

        if (($key = array_search($filename, $fotoKm)) !== false) {
            unset($fotoKm[$key]);
            $serviceRequest->update(['foto_km' => array_values($fotoKm)]);
            Storage::disk('public')->delete('service-requests/km/' . $filename);

            return response()->json(['success' => 'Foto berhasil dihapus']);
        }

        return response()->json(['error' => 'Foto tidak ditemukan'], 404);
    }

    public function deleteFotoEstimasi(Request $request, ServiceRequest $serviceRequest)
    {
        if (!$serviceRequest->isPending()) {
            return response()->json(['error' => 'Tidak dapat menghapus foto'], 403);
        }

        $filename = $request->filename;
        $fotoEstimasi = $serviceRequest->foto_estimasi ?? [];

        if (($key = array_search($filename, $fotoEstimasi)) !== false) {
            unset($fotoEstimasi[$key]);
            $serviceRequest->update(['foto_estimasi' => array_values($fotoEstimasi)]);
            Storage::disk('public')->delete('service-requests/estimates/' . $filename);

            return response()->json(['success' => 'Foto estimasi berhasil dihapus']);
        }

        return response()->json(['error' => 'Foto estimasi tidak ditemukan'], 404);
    }

    public function completeServiceByUser(Request $request, ServiceRequest $serviceRequest)
    {
        if (!$serviceRequest->canCompleteService()) {
            return back()->with('error', 'Service tidak dapat diselesaikan pada status ini.');
        }

        $request->validate([
            'tanggal_selesai_service' => 'required|date',
            'total_service' => 'required|numeric|min:0',
            'catatan_user' => 'required|string',
            'foto_struk_service.*' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'foto_bukti_service.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        DB::beginTransaction();
        try {
            // Upload foto struk service
            $fotoStrukServiceFiles = [];
            if ($request->hasFile('foto_struk_service')) {
                foreach ($request->file('foto_struk_service') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('service-requests/service-receipts', $filename, 'public');
                    $fotoStrukServiceFiles[] = $filename;
                }
            }
            // Upload foto bukti service
            $fotoBuktiServiceFiles = [];
            if ($request->hasFile('foto_bukti_service')) {
                foreach ($request->file('foto_bukti_service') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('service-requests/service-evidence', $filename, 'public');
                    $fotoBuktiServiceFiles[] = $filename;
                }
            }

            $serviceRequest->update([
                'status' => 'service_completed',
                'tanggal_selesai_service' => $request->tanggal_selesai_service,
                'biaya_servis' => $request->total_service,
                'catatan_user' => $request->catatan_user,
                'foto_struk_service' => $fotoStrukServiceFiles,
                'foto_bukti_service' => $fotoBuktiServiceFiles
            ]);

            DB::commit();

            return back()->with('success', 'Service berhasil diselesaikan. Menunggu verifikasi admin.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal menyelesaikan service: ' . $e->getMessage());
        }
    }

    public function verify(Request $request, ServiceRequest $serviceRequest)
    {
        if (!$serviceRequest->canBeVerified()) {
            return back()->with('error', 'Service request tidak dapat diverifikasi.');
        }

        $request->validate([
            'catatan_verifikasi' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $serviceRequest->update([
                'status' => 'completed',
                'catatan_admin' => $request->catatan_verifikasi,
                'verified_by' => Auth::id(),
                'verified_at' => now()
            ]);

            // Create service history entry berdasarkan data yang sudah diinput user
            if ($serviceRequest->tanggal_servis && $serviceRequest->keterangan_servis && $serviceRequest->biaya_servis) {
                $serviceRequest->asset->services()->create([
                    'service_date' => $serviceRequest->tanggal_servis,
                    'description' => $serviceRequest->keterangan_servis,
                    'cost' => $serviceRequest->biaya_servis,
                    'vendor' => 'Service Request - ' . $serviceRequest->nomor_pengajuan
                ]);
            }

            DB::commit();

            return back()->with('success', 'Service berhasil diverifikasi dan ditambahkan ke riwayat service.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal memverifikasi service: ' . $e->getMessage());
        }
    }

    public function servicePendingIndex()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            // Admin can see all service pending and completed requests
            $serviceRequests = ServiceRequest::with(['asset', 'user'])
                                            ->whereIn('status', ['service_pending', 'service_completed'])
                                            ->orderBy('created_at', 'desc')
                                            ->paginate(15);
        } else {
            // Users can only see their own service pending requests
            $serviceRequests = ServiceRequest::with(['asset', 'user'])
                                            ->where('user_id', $user->id)
                                            ->whereIn('status', ['service_pending', 'service_completed'])
                                            ->orderBy('created_at', 'desc')
                                            ->get(); // Change to get() for debugging

            // Debug info
            \Log::info('Service Pending Debug:', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'total_requests' => $serviceRequests->count(),
                'requests' => $serviceRequests->toArray()
            ]);

            // Convert back to paginated
            $serviceRequests = ServiceRequest::with(['asset', 'user'])
                                            ->where('user_id', $user->id)
                                            ->whereIn('status', ['service_pending', 'service_completed'])
                                            ->orderBy('created_at', 'desc')
                                            ->paginate(15);
        }

        return view('service-requests.service-pending', compact('serviceRequests'));
    }

    public function serviceHistoryIndex()
    {
        // Show all completed or verified service requests to all users
        $serviceRequests = ServiceRequest::with(['asset', 'user', 'approver', 'verifier'])
                                ->whereIn('status', ['completed', 'verified'])
                                ->orderBy('verified_at', 'desc')
                                ->orderBy('created_at', 'desc')
                                ->paginate(15);

        return view('service-requests.service-history', compact('serviceRequests'));
    }
    /**
     * Export service history as CSV.
     */
    public function exportHistoryCsv()
    {
        $user = auth()->user();
        if ($user->role === 'admin') {
            $requests = ServiceRequest::with(['asset', 'user', 'approver', 'verifier'])
                ->whereIn('status', ['completed', 'verified'])
                ->orderBy('verified_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $requests = ServiceRequest::with(['asset', 'user', 'approver', 'verifier'])
                ->where('user_id', $user->id)
                ->whereIn('status', ['completed', 'verified'])
                ->orderBy('verified_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        $filename = 'service_history_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];
        $columns = [
            'Nomor Pengajuan','Asset','Serial Number','Nama PIC','Lokasi PIC','Lokasi Project','Status',
            'Keluhan','KM','Deskripsi','Estimasi Harga','Total Biaya','Foto URLs',
            'Catatan Admin','Catatan User','Tanggal Diajukan','Tanggal Disetujui',
            'Tanggal Selesai','Tanggal Terverifikasi'
        ];
        $callback = function() use ($requests, $columns) {
            $file = fopen('php://output', 'w');
            // write UTF-8 BOM for Excel compatibility
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns);
            foreach ($requests as $r) {
                // format currency
                $formatCurrency = function($value) {
                    if ($value === null || $value === '') return '';
                    return 'Rp ' . number_format($value, 0, ',', '.');
                };
                // join photo URLs if accessor exists
                $fotoUrls = '';
                if (isset($r->foto_bukti_service_urls) && is_array($r->foto_bukti_service_urls)) {
                    $fotoUrls = implode(' | ', $r->foto_bukti_service_urls);
                } elseif (!empty($r->foto_bukti_service)) {
                    // fallback: if stored as comma list
                    $fotoUrls = is_array($r->foto_bukti_service) ? implode(' | ', $r->foto_bukti_service) : $r->foto_bukti_service;
                }

                $row = [
                    $r->nomor_pengajuan,
                    trim(($r->asset->merk ?? '') . ' ' . ($r->asset->tipe ?? '')),
                    $r->asset->serial_number ?? '',
                    $r->user->name ?? '',
                    $r->user->lokasi ?? '',
                    $r->lokasi_project ?? '',
                    $r->status ?? '',
                    $r->keluhan,
                    $r->km_saat_ini,
                    $r->deskripsi ?? '',
                    $formatCurrency($r->estimasi_harga),
                    $formatCurrency($r->biaya_servis),
                    $fotoUrls,
                    $r->catatan_admin ?? '',
                    $r->catatan_user ?? '',
                    optional($r->created_at)->format('Y-m-d H:i'),
                    optional($r->approved_at)->format('Y-m-d H:i'),
                    optional($r->tanggal_selesai_service)->format('Y-m-d H:i'),
                    optional($r->verified_at)->format('Y-m-d H:i'),
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };
        // add BOM header for some clients
        $headers['Content-Type'] = 'text/csv; charset=UTF-8';
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export single service request as CSV.
     */
    public function exportDetailCsv(ServiceRequest $serviceRequest)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $serviceRequest->user_id !== $user->id) {
            abort(403);
        }

        $filename = 'service_detail_' . $serviceRequest->nomor_pengajuan . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($serviceRequest) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row with all columns
            fputcsv($file, [
                'Nomor Pengajuan',
                'Status',
                'PIC Pengajuan',
                'Lokasi PIC',
                'Lokasi Project',
                'Nama Kendaraan',
                'Serial Number',
                'KM Saat Ini',
                'Keluhan',
                'Deskripsi',
                'Estimasi Harga',
                'Biaya Service',
                'Tanggal Service',
                'Keterangan Service',
                'Catatan Admin',
                'Catatan User',
                'Tanggal Diajukan',
                'Tanggal Disetujui',
                'Tanggal Selesai Service',
                'Tanggal Terverifikasi'
            ], ';');

            // Data row
            fputcsv($file, [
                $serviceRequest->nomor_pengajuan,
                ucfirst(str_replace('_', ' ', $serviceRequest->status)),
                $serviceRequest->user->name,
                $serviceRequest->user->lokasi ?? '-',
                $serviceRequest->lokasi_project ?? '-',
                trim(($serviceRequest->asset->merk ?? '') . ' ' . ($serviceRequest->asset->tipe ?? '')),
                $serviceRequest->asset->serial_number ?? '-',
                $serviceRequest->km_saat_ini,
                $serviceRequest->keluhan,
                $serviceRequest->deskripsi ?? '-',
                $serviceRequest->estimasi_harga ? 'Rp ' . number_format((float)$serviceRequest->estimasi_harga, 0, ',', '.') : '-',
                $serviceRequest->biaya_servis ? 'Rp ' . number_format((float)$serviceRequest->biaya_servis, 0, ',', '.') : '-',
                $serviceRequest->tanggal_servis ? $serviceRequest->tanggal_servis->format('d/m/Y') : '-',
                $serviceRequest->keterangan_servis ?? '-',
                $serviceRequest->catatan_admin ?? '-',
                $serviceRequest->catatan_user ?? '-',
                $serviceRequest->created_at->format('d/m/Y H:i'),
                $serviceRequest->approved_at ? $serviceRequest->approved_at->format('d/m/Y H:i') : '-',
                $serviceRequest->tanggal_selesai_service ? $serviceRequest->tanggal_selesai_service->format('d/m/Y H:i') : '-',
                $serviceRequest->verified_at ? $serviceRequest->verified_at->format('d/m/Y H:i') : '-'
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Admin: show all service request activities
     */
    public function allActivities(Request $request)
    {
        $status = $request->input('status');
        $month  = $request->input('month');
        $search = $request->input('search');
        $query = ServiceRequest::with(['asset', 'user', 'approver', 'verifier']);
        if ($status) {
            $query->where('status', $status);
        }
        if ($month) {
            $query->whereMonth('created_at', $month)->whereYear('created_at', now()->year);
        }
        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('nik', 'LIKE', '%' . $search . '%');
            });
        }
        $serviceRequests = $query->orderBy('created_at', 'desc')->paginate(15);
        // Tag each service request with a _type attribute for the view
        $serviceRequests->getCollection()->transform(function ($sr) {
            $sr->setAttribute('_type', 'service');
            return $sr;
        });

        // Status distribution data
        $statusData = ServiceRequest::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Monthly counts for this year (service requests only)
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            $monthlyData = ServiceRequest::selectRaw("strftime('%m',created_at) as month, COUNT(*) as total")
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray();
        } else {
            $monthlyData = ServiceRequest::selectRaw("MONTH(created_at) as month, COUNT(*) as total")
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray();
        }

        // Use only service request activities
        $activities = $serviceRequests;

        return view('service-requests.all-activities', compact('activities', 'statusData', 'monthlyData', 'status', 'month', 'search'));
    }

    /**
     * Export all activities to CSV
     */
    public function exportAllActivitiesCsv(Request $request)
    {
        $query = ServiceRequest::with(['asset', 'user']);

        // Apply same filters as allActivities method
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month)->whereYear('created_at', now()->year);
        }
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('nik', 'LIKE', '%' . $request->search . '%');
            });
        }

        $serviceRequests = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="service_all_activities_' . date('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function() use ($serviceRequests) {
            $file = fopen('php://output', 'w');

            // Add BOM for proper UTF-8 encoding in Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV headers
            fputcsv($file, [
                'Nomor Pengajuan',
                'Asset',
                'User',
                'Status',
                'Total Biaya',
                'Tanggal Pengajuan',
                'Keluhan',
                'Jenis Service'
            ], ';');

            foreach ($serviceRequests as $req) {
                $totalBiaya = $req->biaya_servis ?: ($req->estimasi_harga ?: 0);

                fputcsv($file, [
                    $req->nomor_pengajuan,
                    ($req->asset ? $req->asset->merk . ' ' . $req->asset->tipe : '-'),
                    ($req->user ? $req->user->name : '-'),
                    $req->status,
                    'Rp ' . number_format($totalBiaya, 0, ',', '.'),
                    $req->created_at->format('d/m/Y H:i'),
                    $req->keluhan ?: '-',
                    $req->jenis_service ?: '-'
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
