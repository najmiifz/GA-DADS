<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Exports\DynamicAssetExport;
use Maatwebsite\Excel\Facades\Excel;

class AssetPageController extends Controller
{
    public function create()
    {
        $assetTypes = Asset::distinct()->pluck('tipe')->sort();
        return view('asset-pages.create', compact('assetTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:asset_pages,name',
            'asset_type' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'chart_config' => 'nullable|json',
        ]);

        AssetPage::create([
            'name' => $validated['name'],
            'asset_type' => $validated['asset_type'],
            'icon' => $validated['icon'] ?? 'fas fa-file-alt',
            'slug' => Str::slug($validated['name']),
            'chart_config' => json_decode($request->chart_config, true) ?? [],
        ]);

        return redirect()->route('dashboard')->with('success', 'Halaman aset baru berhasil dibuat!');
    }

    public function show(Request $request, $slug)
    {
        $page = AssetPage::where('slug', $slug)->firstOrFail();

        // Base query for assets of the specified type
        $assetsQuery = Asset::where('tipe', $page->asset_type);

        // Get distinct values for filters before applying filters
        $distinctProjects = Asset::where('tipe', $page->asset_type)->distinct()->pluck('project')->sort();
        $distinctLokasi = Asset::where('tipe', $page->asset_type)->distinct()->pluck('lokasi')->sort();

        // Apply filters from request
        if ($request->filled('pic')) {
            $assetsQuery->where('pic', 'like', '%' . $request->pic . '%');
        }
        if ($request->filled('project')) {
            $assetsQuery->where('project', $request->project);
        }
        if ($request->filled('lokasi')) {
            $assetsQuery->where('lokasi', $request->lokasi);
        }

        // Get all filtered assets for chart calculations
        $allFilteredAssets = $assetsQuery->get();

        // Paginate the results
        $paginatedAssets = $assetsQuery->paginate(15)->withQueryString();

        // Calculate chart data based on all filtered assets
        $chartData = [];
        if (!empty($page->chart_config)) {
            foreach ($page->chart_config as $config) {
                $groupBy = $config['group_by'] ?? 'pic';
                $summary = $allFilteredAssets->groupBy($groupBy)->map->count();
                $chartData[] = [
                    'title' => $config['title'],
                    'type' => $config['type'],
                    'data' => $summary,
                ];
            }
        }

        // Statistik aset untuk halaman ini
        $totalAssets     = $allFilteredAssets->count();
        $totalNilai      = $allFilteredAssets->sum('harga_beli');
        $availableAssets = $allFilteredAssets->where('status', 'Available')->count();
        $inUseAssets     = $totalAssets - $availableAssets;
        return view('asset-pages.show', [
            'page' => $page,
            'assets' => $paginatedAssets,
            'chartData' => $chartData,
            'distinctProjects' => $distinctProjects,
            'distinctLokasi' => $distinctLokasi,
            'filters' => $request->only(['pic', 'project', 'lokasi']),
            'totalAssets' => $totalAssets,
            'totalNilai' => $totalNilai,
            'availableAssets' => $availableAssets,
            'inUseAssets' => $inUseAssets,
        ]);
    }

    public function index()
    {
    // Tampilkan hanya halaman unik berdasarkan slug
        $pages = AssetPage::all()->unique('slug')->values();
    return view('asset-pages.index', compact('pages'));
    }

    public function edit(AssetPage $assetPage)
    {
        $assetTypes = Asset::distinct()->pluck('tipe')->sort();
        // Ensure chart_config is available for older records that used 'config' column
        if (empty($assetPage->chart_config) && !empty($assetPage->config)) {
            // if config is stored as JSON string, decode it
            $decoded = is_string($assetPage->config) ? json_decode($assetPage->config, true) : $assetPage->config;
            $assetPage->chart_config = $decoded ?: [];
        }

        return view('asset-pages.edit', ['page' => $assetPage, 'assetTypes' => $assetTypes]);
    }

    public function update(Request $request, AssetPage $assetPage)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:asset_pages,name,' . $assetPage->id,
            'asset_type' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'chart_config' => 'nullable|json',
        ]);

        $assetPage->update([
            'name' => $validated['name'],
            'asset_type' => $validated['asset_type'],
            'icon' => $validated['icon'] ?? 'fas fa-file-alt',
            'slug' => Str::slug($validated['name']),
            'chart_config' => json_decode($request->chart_config, true) ?? [],
        ]);

        return redirect()->route('asset-pages.index')->with('success', 'Halaman aset berhasil diperbarui!');
    }

    public function destroy(AssetPage $assetPage)
    {
        $assetPage->delete();
        return redirect()->route('asset-pages.index')->with('success', 'Halaman aset berhasil dihapus!');
    }

    public function exportCsv(Request $request, $slug)
    {
        $page = AssetPage::where('slug', $slug)->firstOrFail();

        $assetsQuery = Asset::where('tipe', $page->asset_type);

        // Terapkan filter yang sama seperti di metode show
        if ($request->filled('pic')) {
            $assetsQuery->where('pic', 'like', '%' . $request->pic . '%');
        }
        if ($request->filled('project')) {
            $assetsQuery->where('project', $request->project);
        }
        if ($request->filled('lokasi')) {
            $assetsQuery->where('lokasi', $request->lokasi);
        }

        // Support format query param (csv or xlsx). Default to xlsx to preserve styling (filters, autosize, borders)
        $format = strtolower($request->query('format', 'xlsx'));
        $fileName = 'assets-' . $page->slug . '-' . date('Y-m-d') . '.' . ($format === 'csv' ? 'csv' : 'xlsx');

        if ($format === 'csv') {
            return Excel::download(new DynamicAssetExport($assetsQuery), $fileName, \Maatwebsite\Excel\Excel::CSV);
        }
        return Excel::download(new DynamicAssetExport($assetsQuery), $fileName);
    }
}
