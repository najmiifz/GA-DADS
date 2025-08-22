@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Dasbor Splicer</h1>

    {{-- 🔍 Filter --}}
    <form method="GET" action="{{ route('assets.splicers') }}" class="mb-6 grid grid-cols-3 gap-4">
        <input type="text" name="pic" value="{{ request('pic') }}" placeholder="PIC" class="p-2 border rounded">
        <input type="text" name="project" value="{{ request('project') }}" placeholder="Project" class="p-2 border rounded">
        <input type="text" name="lokasi" value="{{ request('lokasi') }}" placeholder="Lokasi" class="p-2 border rounded">
        <button class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
    </form>

    {{-- 📊 Chart --}}
    <div class="bg-white shadow rounded p-4 mb-6">
        <h2 class="font-semibold mb-2">Jumlah Servis Splicer</h2>
        <canvas id="splicerChart"></canvas>
    </div>

    {{-- 📋 Tabel --}}
    <div class="bg-white shadow rounded p-4">
        <h2 class="font-semibold mb-2">Daftar Splicer</h2>
        <table class="min-w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Jenis</th>
                    <th class="border px-4 py-2">PIC</th>
                    <th class="border px-4 py-2">Project</th>
                    <th class="border px-4 py-2">Lokasi</th>
                    <th class="border px-4 py-2">Total Servis</th>
                </tr>
            </thead>
            <tbody>
                @foreach($splicers as $s)
                <tr>
                    <td class="border px-4 py-2">{{ $s->id }}</td>
                    <td class="border px-4 py-2">{{ $s->jenis_aset }}</td>
                    <td class="border px-4 py-2">{{ $s->pic }}</td>
                    <td class="border px-4 py-2">{{ $s->project }}</td>
                    <td class="border px-4 py-2">{{ $s->lokasi }}</td>
                    <td class="border px-4 py-2">{{ $s->total_servis }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const splicerData = @json($servicePerSplicer);

    new Chart(document.getElementById('splicerChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(splicerData),
            datasets: [{ data: Object.values(splicerData), backgroundColor: 'rgba(255,159,64,0.6)' }]
        }
    });
</script>
@endsection
