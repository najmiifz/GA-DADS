@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Dasbor & Tabel Aset</h1>

    {{-- 🔍 Filter --}}
    <form method="GET" action="{{ route('assets.index') }}" class="mb-6 grid grid-cols-5 gap-4">
        <input type="text" name="pic" value="{{ request('pic') }}" placeholder="PIC" class="p-2 border rounded">
        <select name="tipe" class="p-2 border rounded">
            <option value="">-- Tipe --</option>
            <option value="Terpakai" {{ request('tipe')=='Terpakai' ? 'selected':'' }}>Terpakai</option>
            <option value="Tersedia" {{ request('tipe')=='Tersedia' ? 'selected':'' }}>Tersedia</option>
            <option value="Kendaraan" {{ request('tipe')=='Kendaraan' ? 'selected':'' }}>Kendaraan</option>
            <option value="Splicer" {{ request('tipe')=='Splicer' ? 'selected':'' }}>Splicer</option>
        </select>
        <input type="text" name="project" value="{{ request('project') }}" placeholder="Project" class="p-2 border rounded">
        <input type="text" name="lokasi" value="{{ request('lokasi') }}" placeholder="Lokasi" class="p-2 border rounded">
        <button class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
    </form>

    {{-- 📊 Ringkasan --}}
    <div class="grid grid-cols-4 gap-6 mb-6">
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-lg font-semibold">Total Nilai</h2>
            <p class="text-2xl font-bold">Rp{{ number_format($totalNilai,0,',','.') }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-lg font-semibold">Total Aset</h2>
            <p class="text-2xl font-bold">{{ $totalAset }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-lg font-semibold">Terpakai</h2>
            <p class="text-2xl font-bold">{{ $terpakai }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-lg font-semibold">Tersedia</h2>
            <p class="text-2xl font-bold">{{ $tersedia }}</p>
        </div>
    </div>

    {{-- 📊 Chart --}}
    <div class="grid grid-cols-2 gap-6 mb-6">
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-lg font-semibold mb-2">Jenis Aset</h2>
            <canvas id="jenisChart"></canvas>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-lg font-semibold mb-2">Project</h2>
            <canvas id="projectChart"></canvas>
        </div>
    </div>

    {{-- 📊 Lokasi --}}
    <div class="bg-white shadow rounded p-4 mb-6">
        <h2 class="text-lg font-semibold mb-2">Ringkasan Lokasi</h2>
        <table class="min-w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2">Lokasi</th>
                    <th class="border px-4 py-2">Jumlah</th>
                    <th class="border px-4 py-2">Total Nilai</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lokasiSummary as $lokasi)
                <tr>
                    <td class="border px-4 py-2">{{ $lokasi->lokasi }}</td>
                    <td class="border px-4 py-2">{{ $lokasi->jumlah }}</td>
                    <td class="border px-4 py-2">Rp{{ number_format($lokasi->total,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- 📋 Tabel Aset --}}
    <div class="bg-white shadow rounded p-4">
        <h2 class="text-lg font-semibold mb-2">Tabel Aset</h2>
        <table class="min-w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Jenis</th>
                    <th class="border px-4 py-2">PIC</th>
                    <th class="border px-4 py-2">Project</th>
                    <th class="border px-4 py-2">Lokasi</th>
                    <th class="border px-4 py-2">Harga Beli</th>
                    @if(Auth::user()->role == 'admin')
                        <th class="border px-4 py-2">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($assets as $a)
                <tr>
                    <td class="border px-4 py-2">{{ $a->id }}</td>
                    <td class="border px-4 py-2">{{ $a->jenis_aset }}</td>
                    <td class="border px-4 py-2">{{ $a->pic }}</td>
                    <td class="border px-4 py-2">{{ $a->project }}</td>
                    <td class="border px-4 py-2">{{ $a->lokasi }}</td>
                    <td class="border px-4 py-2">Rp{{ number_format($a->harga_beli,0,',','.') }}</td>
                    @if(Auth::user()->role == 'admin')
                        <td class="border px-4 py-2">
                            <a href="{{ route('assets.edit',$a->id) }}" class="text-blue-500">Edit</a> |
                            <form action="{{ route('assets.destroy',$a->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Yakin hapus?')" class="text-red-500">Hapus</button>
                            </form>
                        </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $assets->links() }}
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const jenisData = @json($jenisSummary);
    const projectData = @json($projectSummary);

    new Chart(document.getElementById('jenisChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(jenisData),
            datasets: [{
                label: 'Jumlah',
                data: Object.values(jenisData),
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
            }]
        }
    });

    new Chart(document.getElementById('projectChart'), {
        type: 'pie',
        data: {
            labels: Object.keys(projectData),
            datasets: [{
                data: Object.values(projectData),
                backgroundColor: ['#f87171','#60a5fa','#34d399','#fbbf24','#a78bfa'],
            }]
        }
    });
</script>
@endsection
