@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">All Service Activities</h1>
        <div class="text-sm text-gray-600">
            <i class="fas fa-info-circle mr-1"></i>
            Semua aktivitas pengajuan service tanpa filter status
        </div>
    </div>

    @if(count($activities ?? []) > 0)
    <!-- Charts Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold mb-4">Status Activities</h3>
                <div style="height:300px; position:relative;">
                    <canvas id="activitiesStatusChart"></canvas>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold mb-4">Activities per Bulan</h3>
                <div style="height:300px; position:relative;">
                    <canvas id="activitiesMonthlyChart"></canvas>
                </div>
            </div>
        </div>
    <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <form method="GET" action="{{ route('service-requests.all-activities') }}" class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2">
                    <label for="status_filter" class="text-sm font-medium text-gray-700">Filter Status:</label>
                    <select name="status" id="status_filter" class="px-3 py-2 border border-gray-300 rounded-md text-sm" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        @foreach($statusData as $key => $value)
                            <option value="{{ $key }}" {{ ($status == $key) ? 'selected' : '' }}>{{ ucfirst($key) }} ({{ $value }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label for="month_filter" class="text-sm font-medium text-gray-700">Filter Bulan:</label>
                    <select name="month" id="month_filter" class="px-3 py-2 border border-gray-300 rounded-md text-sm" onchange="this.form.submit()">
                        <option value="">Semua Bulan</option>
                        @for($m=1; $m<=12; $m++)
                            <option value="{{ $m }}" {{ ($month == $m) ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label for="search_filter" class="text-sm font-medium text-gray-700">Cari PIC:</label>
                    <input type="text" name="search" id="search_filter" value="{{ $search ?? '' }}"
                           class="px-3 py-2 border border-gray-300 rounded-md text-sm"
                           placeholder="Nama atau NIK...">
                    <button type="submit" class="bg-blue-600 text-white px-3 py-2 rounded-md hover:bg-blue-700">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                            @if($status || $month || $search)
                                <a href="{{ route('service-requests.all-activities') }}" class="text-sm bg-gray-500 text-white px-3 py-2 rounded-md hover:bg-gray-600">Reset Filter</a>
                            @endif
            </form>
        </div>
                    <form id="deleteForm" action="{{ route('service-requests.bulk-delete') }}" method="POST" class="mb-6">
            @csrf
            @method('DELETE')
            <div class="mb-4 flex gap-2">
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700" onclick="return confirm('Yakin ingin menghapus terpilih?');">
                    <i class="fas fa-trash mr-2"></i>Hapus Terpilih
                </button>
                <a href="{{ route('service-requests.all-activities.export-csv', request()->query()) }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                    <i class="fas fa-download mr-2"></i>Export CSV
                </a>
            </div>
            <div class="overflow-x-auto bg-white rounded-lg shadow-md">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3"><input type="checkbox" id="selectAll" /></th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor / Asset</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User / PIC</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah / Biaya</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($activities as $req)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" name="ids[]" value="{{ $req->id }}" class="rowCheckbox" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ strtoupper($req->_type) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($req->_type === 'service')
                                                {{ $req->nomor_pengajuan ?? '-' }}<br/>
                                                <span class="text-xs text-gray-500">{{ optional($req->asset)->merk }} {{ optional($req->asset)->tipe }}</span>
                                            @else
                                                {{ $req->nomor_pengajuan ?? '-' }}<br/>
                                                <span class="text-xs text-gray-500">Cluster: {{ $req->nama_cluster ?? '-' }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($req->_type === 'service')
                                                {{ optional($req->user)->name }}
                                            @else
                                                {{ optional($req->user)->name ?? ($req->nama_pic ?? '-') }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @php
                                                $state = strtolower($req->status ?? 'pending');
                                            @endphp
                                            @switch($state)
                                                @case('pending')
                                                    <span class="px-2 py-1 leading-tight text-xs rounded-full text-yellow-700 bg-yellow-100">Pending</span>
                                                    @break
                                                @case('delivery')
                                                    <span class="px-2 py-1 leading-tight text-xs rounded-full text-blue-700 bg-blue-100">Delivery</span>
                                                    @break
                                                @case('approved')
                                                    <span class="px-2 py-1 leading-tight text-xs rounded-full text-green-700 bg-green-100">Approved</span>
                                                    @break
                                                @case('received')
                                                    <span class="px-2 py-1 leading-tight text-xs rounded-full text-gray-700 bg-gray-100">Received</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="px-2 py-1 leading-tight text-xs rounded-full text-red-700 bg-red-100">Rejected</span>
                                                    @break
                                                @default
                                                    <span class="px-2 py-1 leading-tight text-xs rounded-full text-gray-700 bg-gray-100">{{ ucfirst($state) }}</span>
                                            @endswitch
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($req->_type === 'service')
                                                @if($req->biaya_servis)
                                                    Rp {{ number_format($req->biaya_servis, 0, ',', '.') }}
                                                @elseif($req->estimasi_harga)
                                                    Rp {{ number_format($req->estimasi_harga, 0, ',', '.') }}
                                                @else
                                                    -
                                                @endif
                                            @else
                                                {{ $req->jumlah_apd ?? '-' }} APD
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($req->created_at)->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if($req->_type === 'service')
                                                <a href="{{ route('service-requests.show', $req->id) }}" class="text-blue-600 hover:underline">Lihat</a>
                                            @else
                                                <a href="{{ route('apd-requests.show', $req->id) }}" class="text-blue-600 hover:underline">Lihat</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                </tbody>
            </table>
        </div>
                    @if(is_object($activities) && method_exists($activities, 'links'))
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            {{ $activities->links() }}
                        </div>
                    @endif
    @else
        <div class="text-center py-12">
            <i class="fas fa-tools text-4xl mb-4 text-gray-400"></i>
            <p class="text-lg text-gray-600">Belum ada aktivitas</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status Chart
    const statusCtx = document.getElementById('activitiesStatusChart').getContext('2d');
    const statusData = @json($statusData);
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusData).map(k => k.charAt(0).toUpperCase() + k.slice(1)),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: ['#fbbf24','#10b981','#ef4444'],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Monthly Chart
    const monthlyCtx = document.getElementById('activitiesMonthlyChart').getContext('2d');
    const monthlyData = @json($monthlyData);
    const monthLabels = Object.keys(monthlyData).map(m => {
        const num = parseInt(m, 10) - 1;
        return new Date(0, num).toLocaleString('id-ID', { month: 'short' });
    });
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Jumlah Aktivitas',
                data: Object.values(monthlyData),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } },
            responsive: true, maintainAspectRatio: false
        }
    });
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = selectAll.checked);
            });
        }
    });
</script>
@endpush
