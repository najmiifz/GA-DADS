@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Dasbor Kendaraan</h1>

    {{-- 🔍 Filter --}}
    <form method="GET" action="{{ route('assets.vehicles') }}" class="mb-6 grid grid-cols-3 gap-4">
        <input type="text" name="pic" value="{{ request('pic') }}" placeholder="PIC" class="p-2 border rounded">
        <input type="text" name="project" value="{{ request('project') }}" placeholder="Project" class="p-2 border rounded">
        <input type="text" name="lokasi" value="{{ request('lokasi') }}" placeholder="Lokasi" class="p-2 border rounded">
        <button class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
    </form>

    {{-- 📊 Chart --}}
    <div class="grid grid-cols-2 gap-6 mb-6">
        <div class="bg-white shadow rounded p-4">
            <h2 class="font-semibold mb-2">Status Pajak Kendaraan</h2>
            <canvas id="taxChart"></canvas>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="font-semibold mb-2">Biaya Servis per Kendaraan</h2>
            <canvas id="serviceChart"></canvas>
        </div>
    </div>

    {{-- 📋 Tabel --}}
    <div class="bg-white shadow rounded p-4">
        <h2 class="font-semibold mb-2">Daftar Kendaraan</h2>
        <table class="min-w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Jenis</th>
                    <th class="border px-4 py-2">PIC</th>
                    <th class="border px-4 py-2">Project</th>
                    <th class="border px-4 py-2">Lokasi</th>
                    <th class="border px-4 py-2">Status Pajak</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicles as $v)
                <tr>
                    <td class="border px-4 py-2">{{ $v->id }}</td>
                    <td class="border px-4 py-2">{{ $v->jenis_aset }}</td>
                    <td class="border px-4 py-2">{{ $v->pic }}</td>
                    <td class="border px-4 py-2">{{ $v->project }}</td>
                    <td class="border px-4 py-2">{{ $v->lokasi }}</td>
                    <td class="border px-4 py-2">{{ $v->status_pajak }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const taxData = @json($taxStatus);
    const serviceData = @json($servicePerVehicle);

    new Chart(document.getElementById('taxChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(taxData),
            datasets: [{ data: Object.values(taxData), backgroundColor: ['#34d399','#f87171','#fbbf24'] }]
        }
    });

    new Chart(document.getElementById('serviceChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(serviceData),
            datasets: [{ data: Object.values(serviceData), backgroundColor: 'rgba(54,162,235,0.6)' }]
        }
    });
</script>
@endsection
