<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\ServiceHistory;
use Illuminate\Http\Request;
use App\Exports\AssetsExport;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Exports\PajakExport;
use App\Exports\ServisExport;
use App\Exports\VehiclesExport;
use App\Models\HolderHistory;
use App\Models\PajakHistory;

class AssetController extends Controller
{
    public function index(Request $request)
    {
    $query = Asset::with('user');


        // default: show newest assets first so newly added items appear on the first page
        if (!$request->filled('sort')) {
            $query->orderBy('created_at', 'desc');
        }

        // filter
        if ($request->filled('pic')) $query->where('pic','like','%'.$request->pic.'%');
        if ($request->filled('tipe')) $query->where('tipe',$request->tipe);
        if ($request->filled('project')) $query->where('project',$request->project);
        if ($request->filled('lokasi')) $query->where('lokasi',$request->lokasi);
        if ($request->filled('jenis_aset')) $query->where('jenis_aset',$request->jenis_aset);

        // sorting
        if ($request->filled('sort')) {
            $query->orderBy($request->sort, $request->get('dir','asc'));
        }

        $assets = $query->paginate(10);

        // Debug: Let's see what we're getting
        foreach($assets as $asset) {
            \Log::info('Controller Asset Debug', [
                'id' => $asset->id,
                'getKey' => $asset->getKey(),
                'exists' => $asset->exists,
                'tipe' => $asset->tipe
            ]);
        }

        // ringkasan
        $totalNilai = Asset::sum('harga_beli');
        $totalAssets  = Asset::count();
        // compute availability based on `pic` value: 'Available' means asset is available
        $availableAssets   = Asset::where('pic','Available')->count();
        $inUseAssets   = Asset::where('pic','<>','Available')->count();

        $jenisSummary   = Asset::selectRaw('jenis_aset, count(*) as total')->groupBy('jenis_aset')->pluck('total','jenis_aset');
        $projectSummary = Asset::selectRaw('project, count(*) as total')->groupBy('project')->pluck('total','project');
        $lokasiSummary  = Asset::selectRaw('lokasi, count(*) as jumlah, sum(harga_beli) as total')->groupBy('lokasi')->get();

        // Data for dropdowns
        $dbTipes = Asset::distinct()->pluck('tipe')->toArray();
        $requiredTipes = ['Kendaraan', 'Elektronik', 'Splicer'];
        $tipes = collect(array_merge($dbTipes, $requiredTipes))->unique()->sort()->values();

        $jenisAsets = Asset::distinct()->whereNotNull('jenis_aset')->pluck('jenis_aset')->sort();

        $dbPics = Asset::distinct()->whereNotNull('pic')->pluck('pic')
            ->filter(function($p){ return strtolower(trim((string)$p)) !== 'super-admin'; })
            ->toArray();
        $pics = collect(array_merge($dbPics, ['Available']))->unique()->sort()->values();

        $projects = Asset::distinct()->whereNotNull('project')->pluck('project')->sort();
        $lokasis = Asset::distinct()->whereNotNull('lokasi')->pluck('lokasi')->sort();

        // Debug: Log asset IDs untuk troubleshooting
        \Log::info('Assets debug:', [
            'total_assets' => $assets->count(),
            'first_asset_id' => $assets->first()?->id ?? 'NULL',
            'first_asset_key' => $assets->first()?->getKey() ?? 'NULL',
            'sample_ids' => $assets->take(3)->pluck('id')->toArray()
        ]);

        return view('dashboard', compact(
            'assets','totalNilai','totalAssets','availableAssets','inUseAssets',
            'jenisSummary','projectSummary','lokasiSummary',
            'tipes', 'jenisAsets', 'pics', 'projects', 'lokasis'
        ));
    }

    public function list(Request $request)
    {
        $query = Asset::query();

        // Role-based filtering: non-admin users only see their assigned assets
        if (auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }

        // filter
        if ($request->filled('pic')) $query->where('pic','like','%'.$request->pic.'%');
        if ($request->filled('tipe')) $query->where('tipe',$request->tipe);
        if ($request->filled('project')) $query->where('project',$request->project);
        if ($request->filled('lokasi')) $query->where('lokasi',$request->lokasi);
        if ($request->filled('jenis_aset')) $query->where('jenis_aset',$request->jenis_aset);

        // sorting
        if ($request->filled('sort')) {
            $query->orderBy($request->sort, $request->get('dir','asc'));
        }

        $assets = $query->paginate(10);

        // ringkasan
        $totalNilai = Asset::sum('harga_beli');
        $totalAset  = Asset::count();
        $terpakai   = Asset::where('tipe','Terpakai')->count();
        $tersedia   = Asset::where('tipe','Tersedia')->count();

        $jenisSummary   = Asset::selectRaw('jenis_aset, count(*) as total')->groupBy('jenis_aset')->pluck('total','jenis_aset');
        $projectSummary = Asset::selectRaw('project, count(*) as total')->groupBy('project')->pluck('total','project');
        $lokasiSummary  = Asset::selectRaw('lokasi, count(*) as jumlah, sum(harga_beli) as total')->groupBy('lokasi')->get();

        return view('assets.index', compact(
            'assets','totalNilai','totalAset','terpakai','tersedia',
            'jenisSummary','projectSummary','lokasiSummary'
        ));
    }

    public function vehicles(Request $request)
    {
        // also eager-load service sum and reimburse sum to avoid N+1 when rendering totals
        $query = Asset::where('tipe','Kendaraan')
            ->with('user')
            ->withSum('services as services_cost_sum', 'cost')
            ->withSum('reimburseRequests as reimburse_cost_sum', 'biaya')
            ->withCount('serviceRequests')
            ->withSum('serviceRequests as service_requests_sum', 'estimasi_harga');
        if ($request->filled('pic')) $query->where('pic','like','%'.$request->pic.'%');
        if ($request->filled('project')) $query->where('project',$request->project);
        if ($request->filled('lokasi')) $query->where('lokasi',$request->lokasi);

        // Filter by jenis_aset if provided
        if ($request->filled('jenis_aset')) {
            $query->where('jenis_aset', $request->jenis_aset);
        }
        // Sorting
        if ($request->get('sort') === 'pajak_terdekat') {
            $query->whereNotNull('tanggal_pajak')->orderBy('tanggal_pajak', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

    $vehicles = $query->paginate(10)->withQueryString();

    $taxStatus = Asset::where('tipe','Kendaraan')
            ->selectRaw('status_pajak, count(*) as total')
            ->groupBy('status_pajak')
            ->pluck('total','status_pajak');

        // Aggregate total servicing cost per asset type for chart by combining:
        // - services costs (services_cost_sum / total_servis)
        // - reimburse costs (reimburse_cost_sum)
        // - service request actual costs (prefer biaya_servis > biaya > estimasi_harga)
        // Include both service histories and service_requests (biaya_servis / estimasi) so
        // "Biaya Servis" reflects real costs for mobil as well as motos with reimburse
        $allVehicles = Asset::where('tipe','Kendaraan')
            ->withSum('services as services_cost_sum', 'cost')
            ->withSum('reimburseRequests as reimburse_cost_sum', 'biaya')
            // sum actual biaya_servis on service_requests (nullable) and estimasi as fallback
            ->withSum('serviceRequests as service_requests_biaya_servis_sum', 'biaya_servis')
            ->withSum('serviceRequests as service_requests_estimasi_sum', 'estimasi_harga')
            ->get()
            // Exclude assets without a merk (brand) so chart groups by valid brand names only
            ->filter(function ($a) {
                return trim((string)($a->merk ?? '')) !== '';
            });

        // Build two series: services totals and reimburse totals grouped by merk
        $serviceTotals = $allVehicles->groupBy('merk')->mapWithKeys(function ($group, $key) {
            $total = $group->sum(function ($a) {
                $histories = (float) ($a->services_cost_sum ?? 0);
                // prefer biaya_servis if present, otherwise use estimasi
                $reqs = (float) ($a->service_requests_biaya_servis_sum ?? 0);
                if ($reqs <= 0) {
                    $reqs = (float) ($a->service_requests_estimasi_sum ?? 0);
                }
                return $histories + $reqs;
            });
            $label = trim((string)$key) === '' ? 'Unknown' : $key;
            return [$label => $total];
        })->toArray();

        $reimburseTotals = $allVehicles->groupBy('merk')->mapWithKeys(function ($group, $key) {
            $total = $group->sum(function ($a) {
                return $a->reimburse_cost_sum ?? 0;
            });
            $label = trim((string)$key) === '' ? 'Unknown' : $key;
            return [$label => $total];
        })->toArray();

        // Remove any Unknown/empty keys from the arrays to avoid showing a blank label on charts
        $serviceTotals = array_filter($serviceTotals, function($v, $k) {
            return trim((string)$k) !== '' && strtolower(trim((string)$k)) !== 'unknown';
        }, ARRAY_FILTER_USE_BOTH);
        $reimburseTotals = array_filter($reimburseTotals, function($v, $k) {
            return trim((string)$k) !== '' && strtolower(trim((string)$k)) !== 'unknown';
        }, ARRAY_FILTER_USE_BOTH);

    // Prepare data for charts
    $taxData = $taxStatus->toArray();
    // Compute display_total_servis per vehicle to keep logic centralized
        foreach ($vehicles as $veh) {
        $jenis = strtolower($veh->jenis_aset ?? '');
        $reimburseSum = $veh->reimburse_cost_sum ?? $veh->reimburseRequests()->sum('biaya');
        $servicesSum = $veh->services_cost_sum ?? $veh->services()->sum('cost') ?? $veh->total_servis ?? 0;
        // Compute total biaya for service requests: prefer biaya_servis, then biaya, then estimasi_harga
        $serviceRequestsTotal = 0;
        // if eager-loaded sum exists for estimasi, use it as fallback
        $estimasiSum = $veh->service_requests_sum ?? 0;
        // compute explicit sums from DB for known columns (biaya_servis, biaya)
        try {
            $sumBiayaServis = $veh->serviceRequests()->sum('biaya_servis');
        } catch (\Exception $e) {
            $sumBiayaServis = 0;
        }
        try {
            $sumBiaya = $veh->serviceRequests()->sum('biaya');
        } catch (\Exception $e) {
            $sumBiaya = 0;
        }
        // choose the most complete column: biaya_servis > biaya > estimasi_harga
        if ($sumBiayaServis > 0) {
            $serviceRequestsTotal = $sumBiayaServis;
        } elseif ($sumBiaya > 0) {
            $serviceRequestsTotal = $sumBiaya;
        } else {
            $serviceRequestsTotal = $estimasiSum;
        }

        $veh->service_requests_total_cost = (float) $serviceRequestsTotal;

        $veh->display_total_servis = (str_contains($jenis, 'motor')) ? (float) $reimburseSum : (float) $servicesSum;
        // Temporary debug logging to trace values when the Vehicles page is loaded.
        try {
            \Log::debug('vehicles-listing', [
                'id' => $veh->id,
                'jenis_aset' => $veh->jenis_aset,
                'display_total_servis' => $veh->display_total_servis,
                'reimburse_cost_sum' => $veh->reimburse_cost_sum ?? 0,
                'service_requests_sum' => $veh->service_requests_sum ?? 0,
                'service_requests_total_cost' => $veh->service_requests_total_cost ?? 0,
                'services_cost_sum' => $veh->services_cost_sum ?? 0,
            ]);
        } catch (\Exception $e) {
            // don't break the page for logging failures
        }
    }
    return view('assets.vehicles', compact('vehicles','taxData','serviceTotals','reimburseTotals','taxStatus'));
    }

    public function splicers(Request $request)
    {
        // Splicers page removed — route and view deleted. Keep method as a 404 fallback if hit.
        abort(404);
    }

    public function show(Asset $asset)
    {
        // Load related data including service histories
        $asset->load('services', 'holderHistories', 'pajakHistory');

        // Check user role to determine which view to show
        if (auth()->user()->role === 'admin') {
            // Admin gets the full management view with activities
            $activities = collect();
            foreach ($asset->services as $s) {
                $activities->push([
                    'date' => $s->service_date,
                    'type' => 'Servis',
                    'description' => ($s->description ?: 'Servis') . ' oleh ' . ($s->vendor ?? 'N/A'),
                    'pic' => $asset->user->name ?? 'N/A',
                ]);
            }
            foreach ($asset->pajakHistory as $p) {
                $activities->push([
                    'date' => $p->tanggal_pajak,
                    'type' => 'Pajak',
                    'description' => 'Pembayaran pajak: Rp ' . number_format($p->jumlah_pajak,0,',','.') . ' (' . $p->status_pajak . ')',
                    'pic' => $asset->user->name ?? 'N/A',
                ]);
            }
            foreach ($asset->holderHistories as $h) {
                $activities->push([
                    'date' => $h->start_date,
                    'type' => 'Perubahan Pemegang',
                    'description' => 'Dipegang oleh ' . $h->holder_name,
                    // store PIC at time of change
                    'pic' => $h->holder_name,
                ]);
            }
            $activities = $activities->sortByDesc('date');
            // Also fetch completed service request history for admin view
            $serviceRequests = \App\Models\ServiceRequest::with('user')
                ->where('asset_id', $asset->id)
                ->whereIn('status', ['completed', 'service_completed'])
                ->orderBy('approved_at', 'desc')
                ->get();
            // Also fetch reimburse requests for this asset (admin view)
            $reimburseRequests = \App\Models\ReimburseRequest::with('user')
                ->where('asset_id', $asset->id)
                ->orderBy('created_at', 'desc')
                ->get();
            return view('assets.show', compact('asset', 'activities', 'serviceRequests', 'reimburseRequests'));
        } else {
            // Regular users get the mobile-friendly detail view with service history
            $serviceHistories = \App\Models\ServiceHistory::where('asset_id', $asset->id)
                ->orderBy('service_date', 'desc')
                ->get();
            // Also load completed service requests for user history
            $serviceRequests = \App\Models\ServiceRequest::where('asset_id', $asset->id)
                ->whereIn('status', ['completed', 'service_completed'])
                ->orderBy('approved_at', 'desc')
                ->get();
            // Fetch reimburse requests for this asset (user view)
            $reimburseRequests = \App\Models\ReimburseRequest::with('user')
                ->where('asset_id', $asset->id)
                ->orderBy('created_at', 'desc')
                ->get();
            return view('assets.show-user', compact('asset', 'serviceHistories', 'serviceRequests', 'reimburseRequests'));
        }
    }

    /**
     * Return asset as JSON for client-side edit modal population.
     */
    public function showJson(Asset $asset)
    {
        // include related service histories so client can populate rows
        $asset->load('services','holderHistories', 'pajakHistory');

        // transform services to include accessible file URL when available
    $assetArr = $asset->toArray();
    // Ensure tanggal_beli is formatted for date inputs
    $assetArr['tanggal_beli'] = $asset->tanggal_beli ? Carbon::parse($asset->tanggal_beli)->format('Y-m-d') : null;
    // Ensure tanggal_pajak is formatted for date inputs
    $assetArr['tanggal_pajak'] = $asset->tanggal_pajak ? Carbon::parse($asset->tanggal_pajak)->format('Y-m-d') : null;
        $assetArr['services'] = $asset->services->map(function($s){
            return [
                'id' => $s->id,
                'service_date' => $s->service_date,
                'description' => $s->description,
                'cost' => $s->cost,
                'vendor' => $s->vendor,
                'file_path' => $s->file_path,
                'file_url' => $s->file_path ? Storage::url($s->file_path) : null,
            ];
        })->all();

        // include holder histories
        $assetArr['holders'] = $asset->holderHistories->map(function($h){
            return [
                'id' => $h->id,
                'holder_name' => $h->holder_name,
                'start_date' => $h->start_date,
                'end_date' => $h->end_date,
                'note' => $h->note,
            ];
        })->all();

    // include pajak history
    $assetArr['pajak_history'] = $asset->pajakHistory->map(function($p){
            return [
                'id' => $p->id,
                // format tanggal_pajak for date input
                'tanggal_pajak' => $p->tanggal_pajak ? Carbon::parse($p->tanggal_pajak)->format('Y-m-d') : null,
                'jumlah_pajak' => $p->jumlah_pajak,
                'status_pajak' => $p->status_pajak,
            ];
        })->all();

    // include public URL for optional asset photo
    $assetArr['foto_aset_url'] = $asset->foto_aset ? Storage::url($asset->foto_aset) : null;
    $assetArr['foto_stnk_url'] = $asset->foto_stnk ? Storage::url($asset->foto_stnk) : null;
    $assetArr['foto_kendaraan_url'] = $asset->foto_kendaraan ? Storage::url($asset->foto_kendaraan) : null;
    return response()->json(['success' => true, 'asset' => $assetArr]);
    }

    /**
     * Show form to create a new asset.
     */
    public function create()
    {
        // Prepare data required by the asset creation form view
        $dbTipes = Asset::distinct()->pluck('tipe')->toArray();
        $requiredTipes = ['Kendaraan', 'Elektronik', 'Splicer'];
        $tipes = collect(array_merge($dbTipes, $requiredTipes))->unique()->sort()->values();

        $jenisAsets = Asset::distinct()->whereNotNull('jenis_aset')->pluck('jenis_aset')->sort()->values();

    // Users for PIC select (only regular users, exclude admins)
    $users = \App\Models\User::where('role', 'user')->orderBy('name')->get();

        $projects = Asset::distinct()->whereNotNull('project')->pluck('project')->sort()->values();
        $lokasis = Asset::distinct()->whereNotNull('lokasi')->pluck('lokasi')->sort()->values();

        return view('assets.create', compact('tipes', 'jenisAsets', 'users', 'projects', 'lokasis'));
    }

    public function store(Request $request)
    {
        // Normalize PIC input from different client-side field names (hidden, select) so server always receives it
        $picCandidate = null;
        if ($request->filled('pic')) {
            $picCandidate = $request->input('pic');
        } elseif ($request->filled('pic_hidden')) {
            $picCandidate = $request->input('pic_hidden');
        } elseif ($request->filled('pic_select')) {
            $picCandidate = $request->input('pic_select');
        } elseif ($request->filled('pic_select[]')) {
            $picCandidate = $request->input('pic_select[]');
        }
        if (!is_null($picCandidate) && $picCandidate !== '') {
            $request->merge(['pic' => $picCandidate]);
        }

        // Log incoming payload for debugging
        Log::info('Asset store payload', $request->all());

        // Sanitize numeric inputs before validation
        $request->merge([
            'harga_beli' => preg_replace('/[^\d]/', '', $request->input('harga_beli', '')),
            'harga_sewa' => preg_replace('/[^\d]/', '', $request->input('harga_sewa', '')),
        ]);

        // Convert empty strings to appropriate defaults for decimal fields to prevent SQL errors
        $decimalFieldsWithDefaultZero = ['harga_beli', 'harga_sewa', 'total_servis'];
        $decimalFieldsNullable = ['jumlah_pajak'];
        foreach ($decimalFieldsWithDefaultZero as $field) {
            if ($request->input($field) === '') {
                $request->merge([$field => 0]);
            }
        }
        foreach ($decimalFieldsNullable as $field) {
            if ($request->input($field) === '') {
                $request->merge([$field => null]);
            }
        }

        try {
            $validated = $request->validate([
            'tipe' => 'nullable|string',
            'jenis_aset' => 'nullable|string',
            'pic' => 'nullable|string',
            'merk' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'plate_number'  => 'nullable|string',
            'status' => 'nullable|string|in:Available,Rusak,Hilang',
            'project' => 'nullable|string',
            'lokasi' => 'nullable|string',
            // prefer `tanggal_beli` (date) for full date input
            'tanggal_beli' => 'nullable|date',
            'tahun_beli' => 'nullable|integer',
            'harga_beli' => 'nullable|numeric',
            'keterangan' => 'nullable|string',
            'harga_sewa' => 'nullable|numeric',
            'tanggal_pajak' => 'nullable|date',
            'jumlah_pajak' => 'nullable|numeric',
            'status_pajak' => 'nullable|string',
            'foto_stnk' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_kendaraan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_aset' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // Validasi untuk file riwayat servis
            'service_file.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5120', // max 5MB per file
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Asset store validation failed', ['errors' => $e->errors()]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

    // Replace "__add__" selections with custom inputs (e.g., tipe_custom)
    foreach (['tipe', 'jenis_aset', 'merk', 'pic', 'project', 'lokasi'] as $field) {
            if (($validated[$field] ?? null) === '__add__') {
                $custom = trim((string) $request->input($field . '_custom', ''));
                if ($custom === '') {
                    $label = str_replace('_', ' ', $field);
                    $errors = [$field => ["Isian baru untuk {$label} harus diisi"]];
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => false, 'errors' => $errors], 422);
                    }
                    return redirect()->back()->withErrors($errors)->withInput();
                }
                $validated[$field] = $custom;
            }
        }
        // If PIC sent as numeric user ID, convert to user name and set user_id
        if (!empty($validated['pic']) && is_numeric($validated['pic'])) {
            $picUser = \App\Models\User::find($validated['pic']);
            if ($picUser) {
                $validated['user_id'] = $picUser->id;
                $validated['pic'] = $picUser->name;
                Log::info('PIC converted', ['user_id' => $picUser->id, 'pic_name' => $picUser->name]);
            }
        }

        // If user selected and lokasi not provided, inherit lokasi from the user (non-destructive)
        if (!empty($validated['user_id']) && empty($validated['lokasi'])) {
            $picUserForLok = \App\Models\User::find($validated['user_id']);
            if ($picUserForLok && !empty($picUserForLok->lokasi)) {
                $validated['lokasi'] = $picUserForLok->lokasi;
            }
        }

        // If user selected and project not provided, inherit project from the user (non-destructive)
        if (!empty($validated['user_id']) && empty($validated['project'])) {
            $picUserForProject = \App\Models\User::find($validated['user_id']);
            if ($picUserForProject && !empty($picUserForProject->project)) {
                $validated['project'] = $picUserForProject->project;
            }
        }

        // Handle special case for status-based PIC setting
        if (!empty($validated['status'])) {
            if (in_array($validated['status'], ['Available', 'Rusak', 'Hilang'])) {
                $validated['pic'] = $validated['status'];
                $validated['user_id'] = null; // Clear user_id for status-based PICs
                Log::info('Status-based PIC set', ['status' => $validated['status']]);
            }
        }

        Log::info('Final validated data', $validated);

        // ensure tanggal_beli exists: prefer explicit tanggal_beli, otherwise derive from tahun_beli
        if (empty($validated['tanggal_beli']) && !empty($validated['tahun_beli'])) {
            $validated['tanggal_beli'] = sprintf('%04d-01-01', intval($validated['tahun_beli']));
        }

        // keep tahun_beli in sync if only tanggal_beli provided
        if (empty($validated['tahun_beli']) && !empty($validated['tanggal_beli'])) {
            try {
                $validated['tahun_beli'] = Carbon::parse($validated['tanggal_beli'])->year;
            } catch (\Exception $e) {
                // ignore parse error; validation already ensured it's a date
            }
        }

        try {
            // Note: serial_number will be generated after creating the asset using
            // the asset's auto-increment id to ensure a global sequential number
            // across all asset types. If the client explicitly provided
            // serial_number we will keep it.
            // Create asset with provided data
            $asset = Asset::create($validated);
            // If admin created the asset and explicitly provided a project, update the user's project
            if (!empty($asset->user_id) && array_key_exists('project', $validated) && !empty($validated['project'])) {
                $userForAsset = \App\Models\User::find($asset->user_id);
                if ($userForAsset) {
                    $userForAsset->project = $validated['project'];
                    $userForAsset->save();
                    // propagate to other assets of the same user
                    Asset::where('user_id', $asset->user_id)
                        ->where('id', '<>', $asset->id)
                        ->update(['project' => $validated['project']]);
                    // ensure the created asset has the project recorded
                    $asset->project = $validated['project'];
                    $asset->save();
                }
            } else {
                // Ensure asset.project follows assigned user's project when possible (fallback)
                if (!empty($asset->user_id) && empty($asset->project)) {
                    $userForAsset = \App\Models\User::find($asset->user_id);
                    if ($userForAsset && !empty($userForAsset->project)) {
                        $asset->project = $userForAsset->project;
                        $asset->save();
                    }
                }
            }
            Log::info('Asset created', ['id' => $asset->id]);

            // Serial number assignment is handled by AssetObserver after create.

            // Handle vehicle photo uploads for Kendaraan type
            // Handle foto_aset upload for any asset type
            if ($request->hasFile('foto_aset')) {
                try {
                    $fotoAsetPath = $request->file('foto_aset')->store('asset_photos', 'public');
                    Log::info('foto_aset store result', ['path' => $fotoAsetPath]);
                    $asset->foto_aset = $fotoAsetPath;
                    $asset->save();
                    $exists = Storage::disk('public')->exists($fotoAsetPath);
                    Log::info('foto_aset exists after store', ['path' => $fotoAsetPath, 'exists' => $exists]);
                } catch (\Exception $e) {
                    Log::error('Failed to store foto_aset', ['error' => $e->getMessage()]);
                }
            }
            if (strtolower($asset->tipe) === 'kendaraan') {
                if ($request->hasFile('foto_stnk')) {
                    try {
                        $fotoStnkPath = $request->file('foto_stnk')->store('vehicle_photos', 'public');
                        Log::info('foto_stnk store result', ['path' => $fotoStnkPath]);
                        $asset->foto_stnk = $fotoStnkPath;
                        $existsStnk = Storage::disk('public')->exists($fotoStnkPath);
                        Log::info('foto_stnk exists after store', ['path' => $fotoStnkPath, 'exists' => $existsStnk]);
                    } catch (\Exception $e) {
                        Log::error('Failed to store foto_stnk', ['error' => $e->getMessage()]);
                    }
                }

                if ($request->hasFile('foto_kendaraan')) {
                    try {
                        $fotoKendaraanPath = $request->file('foto_kendaraan')->store('vehicle_photos', 'public');
                        Log::info('foto_kendaraan store result', ['path' => $fotoKendaraanPath]);
                        $asset->foto_kendaraan = $fotoKendaraanPath;
                        $existsKend = Storage::disk('public')->exists($fotoKendaraanPath);
                        Log::info('foto_kendaraan exists after store', ['path' => $fotoKendaraanPath, 'exists' => $existsKend]);
                    } catch (\Exception $e) {
                        Log::error('Failed to store foto_kendaraan', ['error' => $e->getMessage()]);
                    }
                }

                // Save photo paths if any were uploaded
                if ($request->hasFile('foto_stnk') || $request->hasFile('foto_kendaraan')) {
                    $asset->save();
                }
            }

            // Create initial holder history record
            if ($asset->user) {
                HolderHistory::create([
                    'asset_id'    => $asset->id,
                    'holder_name' => $asset->user->name,
                    'start_date'  => Carbon::parse($asset->created_at)->toDateString(),
                    'note'        => 'Initial assignment of PIC',
                ]);
                // Sync user's lokasi with the asset lokasi on initial assignment
                if (!empty($asset->lokasi)) {
                    \App\Models\User::where('id', $asset->user->id)->update(['lokasi' => $asset->lokasi]);
                }
            }

            // After creating asset, save service histories for Kendaraan & Splicer or if service data provided
            $tipeLower = strtolower($asset->tipe ?? '');
            // Auto-enable services for vehicle/splicer types or if service data exists
            $hasServiceData = $request->has('service_date') && !empty(array_filter($request->input('service_date', [])));
            $hasService = in_array($tipeLower, ['kendaraan','splicer']) || $hasServiceData;
            if ($hasService) {
                $dates = $request->input('service_date', []);
                $descs = $request->input('service_desc', []);
                $costs = $request->input('service_cost', []);
                $vendors = $request->input('service_vendor', []);
                $files = $request->file('service_file', []);
                $count = max(count($dates), count($descs), count($costs), count($vendors));

                for ($i = 0; $i < $count; $i++) {
                    $date = $dates[$i] ?? null;
                    $desc = $descs[$i] ?? null;
                    $cost = $costs[$i] ?? null;
                    $vendor = $vendors[$i] ?? null;
                    $filePath = null;

                    if (isset($files[$i]) && $files[$i]) {
                        $filePath = $files[$i]->store('service_files', 'public');
                    }

                    // create new if at least one field is non-empty
                    if ($date || $desc || $cost || $vendor || $filePath) {
                        ServiceHistory::create([
                            'asset_id' => $asset->id,
                            'service_date' => $date,
                            'description' => $desc,
                            'cost' => $cost,
                            'vendor' => $vendor,
                            'file_path' => $filePath,
                        ]);
                    }
                }

                // Recalculate total_servis after insert
                $asset->recalculateTotalServis();
            }
        } catch (\Exception $ex) {
            Log::error('Failed to create asset', ['exception' => $ex->getMessage()]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan asset', 'error' => $ex->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan asset: '.$ex->getMessage())->withInput();
        }

        if ($request->ajax() || $request->wantsJson()) {
            // strip status from returned payload to avoid client-side expecting it
            $asset->load('services');
            $data = $asset->toArray();
            unset($data['status']);
            return response()->json(['success' => true, 'message' => 'Asset berhasil ditambahkan', 'asset' => $data]);
        }

    // After creating asset via standard form submit, redirect to the asset detail page
    // After creating asset, return to the creation form for quick add
    // Redirect back to the asset creation form for quick entry
    // After creating asset, redirect to the asset management list
    return redirect()->route('assets.index')->with('success', 'Asset berhasil ditambahkan');
    }

    public function edit(Asset $asset)
    {
        // Debug logging
        \Log::info('Asset edit access check', [
            'asset_id' => $asset->id,
            'asset_user_id' => $asset->user_id,
            'current_user_id' => auth()->id(),
            'current_user_role' => auth()->user()->role,
            'current_user_email' => auth()->user()->email
        ]);

        // Check access: admin dapat edit semua, user hanya bisa edit asset yang di-assign ke mereka
        if (auth()->user()->role !== 'admin' && $asset->user_id !== auth()->id()) {
            \Log::warning('Access denied', [
                'reason' => 'User not admin and asset not assigned to user',
                'user_role' => auth()->user()->role,
                'asset_user_id' => $asset->user_id,
                'current_user_id' => auth()->id()
            ]);
            abort(403, 'Anda tidak memiliki akses untuk mengedit asset ini.');
        }

        // Load related data including service histories
        $asset->load('services', 'holderHistories', 'pajakHistory');

        // Data for dropdowns (same as create)
        $dbTipes = Asset::distinct()->pluck('tipe')->toArray();
        $requiredTipes = ['Kendaraan', 'Elektronik', 'Splicer'];
        $tipes = collect(array_merge($dbTipes, $requiredTipes))->unique()->sort()->values();
        $jenisAsets = Asset::distinct()->whereNotNull('jenis_aset')->pluck('jenis_aset')->sort();
        $dbPics = Asset::distinct()->whereNotNull('pic')->pluck('pic')
            ->filter(function($p){ return strtolower(trim((string)$p)) !== 'super-admin'; })
            ->toArray();
        $pics = collect(array_merge($dbPics, ['Available']))->unique()->sort()->values();
        $projects = Asset::distinct()->whereNotNull('project')->pluck('project')->sort();
        $lokasis = Asset::distinct()->whereNotNull('lokasi')->pluck('lokasi')->sort();

        // Users untuk assignment (hanya untuk admin)
        $users = collect();
        if (auth()->user()->role === 'admin') {
            $users = \App\Models\User::where('role', 'user')->get();
        }

        return view('assets.edit', compact(
            'asset', 'tipes', 'jenisAsets', 'pics', 'projects', 'lokasis', 'users'
        ));
    }

    public function update(Request $request, Asset $asset)
    {
        // Check access: admin dapat edit semua, user hanya bisa edit asset yang di-assign ke mereka
        if (auth()->user()->role !== 'admin' && $asset->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit asset ini.');
        }

        // Role-based validation rules
        if (auth()->user()->role === 'admin') {
            // Admin bisa edit semua field, termasuk status/PIC
            $rules = [
                'tipe' => 'nullable|string',
                'jenis_aset' => 'nullable|string',
                'merk' => 'nullable|string',
                'serial_number' => 'nullable|string',
                'plate_number'  => 'nullable|string',
                'project' => 'nullable|string',
                'lokasi' => 'nullable|string',
                'tanggal_beli' => 'nullable|date',
                'harga_beli' => 'nullable|numeric',
                'harga_sewa' => 'nullable|numeric',
                'status_pajak' => 'nullable|string',
                'tanggal_pajak' => 'nullable|date',
                'jumlah_pajak' => 'nullable|numeric',
                'keterangan' => 'nullable|string',
                'pajak_id' => 'nullable|integer',
                'foto_stnk' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'foto_kendaraan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'foto_aset' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'pic' => 'nullable|string',
                'status' => 'nullable|string|in:Available,Rusak,Hilang',
            ];
        } else {
            // User/PIC only edits description, tax data, and can upload asset and STNK photos
            $rules = [
                'keterangan'   => 'nullable|string',
                'status_pajak' => 'nullable|string',
                'tanggal_pajak'=> 'nullable|date',
                'jumlah_pajak' => 'nullable|numeric',
                'pajak_id'     => 'nullable|integer',
                'foto_aset'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'foto_stnk'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ];
        }

        if ($request->hasFile('service_file')) {
            $rules['service_file.*'] = 'nullable|file|mimes:pdf,jpg,jpeg,png,xls,xlsx,doc,docx|max:10240';
        }

        // Convert empty strings to appropriate defaults for decimal fields to prevent SQL errors
        $decimalFieldsWithDefaultZero = ['harga_beli', 'harga_sewa', 'total_servis'];
        $decimalFieldsNullable = ['jumlah_pajak'];
        foreach ($decimalFieldsWithDefaultZero as $field) {
            if ($request->input($field) === '') {
                $request->merge([$field => 0]);
            }
        }
        foreach ($decimalFieldsNullable as $field) {
            if ($request->input($field) === '') {
                $request->merge([$field => null]);
            }
        }

    $validated = $request->validate($rules);
    // Capture old values for comparison
    $oldUserId = $asset->user_id;
    $oldLokasi  = $asset->lokasi;
        // Jika admin, jaga nilai harga agar tidak null—fallback ke nilai lama saat kosong
        if (auth()->user()->role === 'admin') {
            // Only fallback if the field was not provided or is null (not if it's 0)
            if (!array_key_exists('harga_beli', $validated) || $validated['harga_beli'] === null) {
                $validated['harga_beli'] = $asset->harga_beli;
            }
            if (!array_key_exists('harga_sewa', $validated) || $validated['harga_sewa'] === null) {
                $validated['harga_sewa'] = $asset->harga_sewa;
            }
        }

        // Role-based field filtering
        if (auth()->user()->role !== 'admin') {
            // User hanya bisa update field tertentu
            $allowedFields = ['keterangan', 'status_pajak', 'tanggal_pajak', 'jumlah_pajak', 'pajak_id'];
            $validated = array_intersect_key($validated, array_flip($allowedFields));
        } else {
            // Admin bisa edit semua, handle custom inputs
            foreach (['tipe', 'jenis_aset', 'merk', 'pic', 'project', 'lokasi'] as $field) {
                if (($validated[$field] ?? null) === '__add__') {
                    $custom = trim((string) $request->input($field . '_custom', ''));
                    if ($custom === '') {
                        $label = str_replace('_', ' ', $field);
                        $errors = [$field => ["Isian baru untuk {$label} harus diisi"]];
                        if ($request->ajax() || $request->wantsJson()) {
                            return response()->json(['success' => false, 'errors' => $errors], 422);
                        }
                        return redirect()->back()->withErrors($errors)->withInput();
                    }
                    $validated[$field] = $custom;
                }
            }
        }

        // Capture previous PIC assignment (admin only)
        if (auth()->user()->role === 'admin') {
            $oldUserId = $asset->user_id;
            $oldStatus = $asset->pic;
            // Map new PIC input to user_id or status
            if (isset($validated['pic'])) {
                $picInput = $validated['pic'];
                if (strpos($picInput, 'user:') === 0) {
                    $validated['user_id'] = intval(substr($picInput, 5));
                    unset($validated['pic']);
                } else {
                    // status values
                    $validated['user_id'] = null;
                    // also save selected status into status field so it shows in tables
                    $validated['status'] = $picInput;
                }
            }
        }

    // Update asset fields
    // Capture old project for comparison
    $oldProject = $asset->project;
    $asset->update($validated);
        // Ensure project follows user assignment when admin changes PIC or project
        if (auth()->user()->role === 'admin') {
            // If PIC assigned and user's project is empty or different and asset project exists, update user
            if (!empty($asset->user_id) && array_key_exists('project', $validated)) {
                $userForAsset = \App\Models\User::find($asset->user_id);
                if ($userForAsset) {
                    // If the asset's project changed, update the user's project to match
                    if ($asset->project !== $oldProject) {
                        $userForAsset->project = $asset->project;
                        $userForAsset->save();
                        // propagate to other assets of the same user as well
                        Asset::where('user_id', $asset->user_id)
                            ->where('id', '<>', $asset->id)
                            ->update(['project' => $asset->project]);
                    } else {
                        // If asset project unchanged but user has no project, set it
                        if (empty($userForAsset->project) && !empty($asset->project)) {
                            $userForAsset->project = $asset->project;
                            $userForAsset->save();
                        }
                    }
                }
            }
        }
        // Always synchronize asset lokasi and user lokasi for admins
        if (auth()->user()->role === 'admin' && $asset->user_id) {
            // Set user.lokasi to asset.lokasi
            \App\Models\User::where('id', $asset->user_id)
                ->update(['lokasi' => $asset->lokasi]);
            // Propagate lokasi to all other assets of the same user
            Asset::where('user_id', $asset->user_id)
                ->where('id', '<>', $asset->id)
                ->update(['lokasi' => $asset->lokasi]);
        }
        // Log user activity when asset is updated
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Updated Asset',
            'description' => 'Asset ID ' . $asset->id . ' updated by User ' . auth()->id(),
        ]);
        // Handle PIC change in history (admin only)
        if (auth()->user()->role === 'admin') {
            // Determine new assignment
            $newUserId = $asset->user_id;
            $newStatus = $asset->pic;
            // If holder changed (by user_id only)
            if ($oldUserId !== $newUserId) {
                // Close previous history entry
                $lastHistory = HolderHistory::where('asset_id', $asset->id)
                                  ->whereNull('end_date')
                                  ->latest('start_date')
                                  ->first();
                if ($lastHistory) {
                    $lastHistory->end_date = Carbon::now()->toDateString();
                    $lastHistory->save();
                }
                // Create new history entry for new holder
                $displayName = $newUserId
                    ? \App\Models\User::find($newUserId)->name
                    : $newStatus;
                HolderHistory::create([
                    'asset_id'    => $asset->id,
                    'holder_name' => $displayName,
                    'start_date'  => Carbon::now()->toDateString(),
                    'note'        => 'PIC diubah menjadi ' . $displayName,
                ]);
            }
        }
        // Always append new service history entries
        $dates   = $request->input('service_date', []);
        $descs   = $request->input('service_desc', []);
        $costs   = $request->input('service_cost', []);
        $vendors = $request->input('service_vendor', []);
        $files   = $request->file('service_file', []);
        $count   = max(count($dates), count($descs), count($costs), count($vendors));

        for ($i = 0; $i < $count; $i++) {
            $date     = $dates[$i] ?? null;
            $desc     = $descs[$i] ?? null;
            $cost     = $costs[$i] ?? null;
            $vendor   = $vendors[$i] ?? null;
            $filePath = isset($files[$i]) && $files[$i] ? $files[$i]->store('service_files', 'public') : null;
            if ($date || $desc || $cost || $vendor) {
                ServiceHistory::create([
                    'asset_id'     => $asset->id,
                    'service_date' => $date,
                    'description'  => $desc,
                    'cost'         => $cost,
                    'vendor'       => $vendor,
                    'file_path'    => $filePath,
                ]);
            }
        }

        // Recalculate total service cost
        $asset->recalculateTotalServis();

        // Return response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Asset berhasil diperbarui',
            ]);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Asset berhasil diperbarui');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        // If called via AJAX, return JSON for client-side handling
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Asset berhasil dihapus'
            ]);
        }
        return redirect()->route('dashboard')->with('success', 'Asset berhasil dihapus');
    }

    public function bulkDestroy(Request $request)
    {
        $assetIds = json_decode($request->input('asset_ids'), true);

        if (empty($assetIds) || !is_array($assetIds)) {
            return redirect()->back()->with('error', 'Tidak ada asset yang dipilih untuk dihapus');
        }

        try {
            // Validate all asset IDs exist and user has permission to delete them
            $assets = Asset::whereIn('id', $assetIds)->get();

            if ($assets->count() !== count($assetIds)) {
                return redirect()->back()->with('error', 'Beberapa asset tidak ditemukan');
            }

            // Delete all selected assets
            Asset::whereIn('id', $assetIds)->delete();

            $deletedCount = count($assetIds);
            return redirect()->back()->with('success', "Berhasil menghapus {$deletedCount} asset");

        } catch (\Exception $e) {
            Log::error('Bulk delete error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus asset');
        }
    }

    public function export(Request $request)
    {
        $type = $request->query('type', 'basic');
        $filters = $request->only(['pic', 'tipe', 'project', 'lokasi', 'jenis_aset', 'pic_exact', 'pic_not']);
        // Support format query param (csv or xlsx). Default to xlsx to preserve styling (filters, autosize, borders)
        $format = strtolower($request->query('format', 'xlsx'));
        $filename = 'assets-' . date('Y-m-d-H-i-s') . '.' . ($format === 'csv' ? 'csv' : 'xlsx');
        if ($format === 'csv') {
            return Excel::download(new AssetsExport($filters, $type), $filename, \Maatwebsite\Excel\Excel::CSV);
        }
        return Excel::download(new AssetsExport($filters, $type), $filename);
    }

    public function exportVehicles(Request $request)
    {
        $filters = $request->only(['pic', 'project', 'lokasi']);
        $format = strtolower($request->query('format', 'xlsx'));
        $filename = 'vehicles-' . date('Y-m-d-H-i-s') . '.' . ($format === 'csv' ? 'csv' : 'xlsx');
        if ($format === 'csv') {
            return Excel::download(new VehiclesExport($filters), $filename, \Maatwebsite\Excel\Excel::CSV);
        }
        return Excel::download(new VehiclesExport($filters), $filename);
    }

    public function exportSplicers(Request $request)
    {
        // Splicers export removed with the page; return 404 to indicate not found.
        abort(404);
    }

    public function exportPajak(Request $request, Asset $asset)
    {
        // Export pajak data for a single asset (detail page export)
        $format = strtolower($request->query('format', 'xlsx'));
        $filename = 'pajak-' . $asset->id . '.' . $format;
        if ($format === 'csv') {
            return Excel::download(new PajakExport($asset), $filename, \Maatwebsite\Excel\Excel::CSV);
        }
        return Excel::download(new PajakExport($asset), $filename);
    }

    public function exportServis(Request $request, Asset $asset)
    {
        // Export service histories for a single asset with optional date filtering
        $query = $asset->services();

        if ($request->filled('start_date')) {
            $query->whereDate('service_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('service_date', '<=', $request->input('end_date'));
        }

        $services = $query->get();

        $format = strtolower($request->query('format', 'xlsx'));
        $filename = 'servis-' . $asset->id . '.' . $format;
        if ($format === 'csv') {
            return Excel::download(new ServisExport($services), $filename, \Maatwebsite\Excel\Excel::CSV);
        }
        return Excel::download(new ServisExport($services), $filename);
    }

    /**
     * Export a single asset detail (metadata + service history) as a CSV formatted for Excel.
     */
    public function exportDetailCsv(Request $request, Asset $asset)
    {
        $asset->load('services', 'holderHistories', 'pajakHistory', 'user');

        $filename = 'asset-' . $asset->id . '-' . (Str::slug($asset->merk ?? 'asset')) . '.csv';

        $callback = function () use ($asset) {
            // open output stream
            $out = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel recognizes encoding
            fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Asset metadata as a single horizontal table: header row then value row
            $metaHeaders = [
                'ID', 'Merk', 'Jenis Aset', 'Tipe', 'Serial Number', 'Plate Number',
                'PIC', 'Project', 'Lokasi', 'Tanggal Beli', 'Harga Beli (raw)', 'Harga Beli (display)', 'Keterangan'
            ];
            // compute values into variables for readability and to avoid parser issues
            $metaPicName = optional($asset->user)->name ?? $asset->pic ?? '';
            $metaTanggalBeli = $asset->tanggal_beli ? Carbon::parse($asset->tanggal_beli)->format('Y-m-d') : '';
            $metaHargaRaw = $asset->harga_beli ?? '0';
            $metaHargaDisplay = $asset->harga_beli ? 'Rp ' . number_format($asset->harga_beli, 0, ',', '.') : '0';

            $metaValues = [
                $asset->id,
                $asset->merk ?? '',
                $asset->jenis_aset ?? '',
                $asset->tipe ?? '',
                $asset->serial_number ?? '',
                $asset->plate_number ?? '',
                $metaPicName,
                $asset->project ?? '',
                $asset->lokasi ?? '',
                $metaTanggalBeli,
                $metaHargaRaw,
                $metaHargaDisplay,
                $asset->keterangan ?? ''
            ];

            // use semicolon delimiter for Excel regional settings
            fputcsv($out, $metaHeaders, ';');
            fputcsv($out, $metaValues, ';');

            // blank line
            // blank line
            fputcsv($out, [], ';');

            // Service history header and column titles
            // service history title row
            fputcsv($out, ['Riwayat Service'], ';');
            // service history headers
            fputcsv($out, ['Tanggal Service', 'Deskripsi', 'Vendor', 'Total Biaya', 'File URL'], ';');

            foreach ($asset->services as $s) {
                $date = $s->service_date ? Carbon::parse($s->service_date)->format('Y-m-d') : '';
                $fileUrl = $s->file_path ? Storage::url($s->file_path) : '';
                // Ensure cost is numeric so Excel can treat it as number
                $costRaw = is_numeric($s->cost) ? $s->cost : (float) preg_replace('/[^0-9\.\-]/', '', (string) $s->cost);
                fputcsv($out, [$date, $s->description ?? '', $s->vendor ?? '', $costRaw, $fileUrl], ';');
            }

            // close stream
            fclose($out);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function getFilteredData(Request $request)
    {
        $query = Asset::query();

        if ($request->filled('pic')) {
            $query->where('pic', $request->pic);
        }
        if ($request->filled('project')) {
            $query->where('project', $request->project);
        }
        if ($request->filled('lokasi')) {
            $query->where('lokasi', $request->lokasi);
        }

        $assets = $query->get();

        // Recalculate summaries based on filtered data
        $totalNilai = $assets->sum('harga_beli');
        $totalAssets = $assets->count();
        $availableAssets = $assets->where('pic', 'Available')->count();
        $inUseAssets = $totalAssets - $availableAssets;

        $jenisSummary = $assets->groupBy('jenis_aset')->map->count();
        $projectSummary = $assets->groupBy('project')->map->count();
        $lokasiSummary = $assets->groupBy('lokasi')->map(function ($group) {
            return [
                'jumlah' => $group->count(),
                'total' => $group->sum('harga_beli')
            ];
        });

        return response()->json([
            'stats' => [
                'totalNilai' => 'Rp ' . number_format($totalNilai, 0, ',', '.'),
                'totalAssets' => $totalAssets,
                'availableAssets' => $availableAssets,
                'inUseAssets' => $inUseAssets,
            ],
            'charts' => [
                'jenis' => $jenisSummary,
                'project' => $projectSummary,
                'lokasi' => $lokasiSummary,
            ]
        ]);
    }
    /**
     * Return next serial number for a given asset type.
     */
    public function nextSerial($jenis)
    {
        // Mapping codes for asset types
        $codes = [
            'Laptop'    => 'LAP',
            'Handphone' => 'HP',
            'Splicer'   => 'SPLCR',
            'Otdr'      => 'OTDR',
            'Ols'       => 'OLS',
            'Opm'       => 'OPM',
            'Motor'     => 'MTR',
            'Mobil'     => 'MBL',
            'Furniture' => 'FRNTR',
        ];
    $code = $codes[$jenis] ?? strtoupper(substr(preg_replace('/\s+/', '', $jenis), 0, 3));
    // Use the next auto-increment id as the global sequence preview.
    // NOTE: this is a best-effort preview; in high-concurrency environments
    // it's safer to derive serials after insert using the created id.
    $maxId = Asset::max('id') ?? 0;
    $nextId = $maxId + 1;
    $prefix = str_pad($nextId, 3, '0', STR_PAD_LEFT);
    return response()->json(['next' => "{$prefix}/{$code}"]);
    }
}
