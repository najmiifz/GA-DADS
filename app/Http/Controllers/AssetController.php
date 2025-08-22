<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use App\Exports\AssetsExport;
use Maatwebsite\Excel\Facades\Excel;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::query();

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
        $query = Asset::where('tipe','Kendaraan');
        if ($request->filled('pic')) $query->where('pic','like','%'.$request->pic.'%');
        if ($request->filled('project')) $query->where('project',$request->project);
        if ($request->filled('lokasi')) $query->where('lokasi',$request->lokasi);

        $vehicles = $query->get();

        $taxStatus = Asset::where('tipe','Kendaraan')
            ->selectRaw('status_pajak, count(*) as total')
            ->groupBy('status_pajak')
            ->pluck('total','status_pajak');

        $servicePerVehicle = Asset::where('tipe','Kendaraan')
            ->pluck('total_servis','jenis_aset');

        return view('assets.vehicles', compact('vehicles','taxStatus','servicePerVehicle'));
    }

    public function splicers(Request $request)
    {
        $query = Asset::where('tipe','Splicer');
        if ($request->filled('pic')) $query->where('pic','like','%'.$request->pic.'%');
        if ($request->filled('project')) $query->where('project',$request->project);
        if ($request->filled('lokasi')) $query->where('lokasi',$request->lokasi);

        $splicers = $query->get();

        $servicePerSplicer = Asset::where('tipe','Splicer')
            ->pluck('total_servis','jenis_aset');

        return view('assets.splicers', compact('splicers','servicePerSplicer'));
    }

    public function create()
    {
        return view('assets.create');
    }

    public function store(Request $request)
    {
        Asset::create($request->all());
        return redirect()->route('assets.index');
    }

    public function edit(Asset $asset)
    {
        return view('assets.edit',compact('asset'));
    }

    public function update(Request $request, Asset $asset)
    {
        $asset->update($request->all());
        return redirect()->route('assets.index');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('assets.index');
    }

    public function export()
    {
        return Excel::download(new AssetsExport, 'assets.xlsx');
    }
}
