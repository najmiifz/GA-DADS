@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 overflow-x-hidden">
    <div class="w-full max-w-none px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                @if($statusFilter === 'pending')
                    Pengajuan APD
                    <span class="text-lg text-yellow-600">(Menunggu Persetujuan)</span>
                @elseif($statusFilter === 'all')
                    Semua Aktivitas Pengajuan APD
                @else
                    Aktivitas Pengajuan APD ({{ ucfirst($statusFilter) }})
                @endif
            </h1>
        </div>
        @if($statusFilter === 'pending')
            <div class="text-sm text-gray-600 mb-4">
                Kelola pengajuan APD yang menunggu persetujuan oleh admin.
            </div>
        @elseif($statusFilter === 'all')
            <div class="text-sm text-gray-600 mb-4">
                Melihat semua aktivitas pengajuan APD (termasuk pending, disetujui, dan ditolak).
            </div>
        @endif

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-500 text-white p-6 rounded-lg shadow-md">
            <div class="text-lg font-semibold">{{ $requests->count() }}</div>
            <div class="text-sm">Total Pengajuan</div>
        </div>
        <div class="bg-yellow-500 text-white p-6 rounded-lg shadow-md">
            <div class="text-lg font-semibold">{{ $requests->where('status', 'pending')->count() }}</div>
            <div class="text-sm">Pending</div>
        </div>
        <div class="bg-green-500 text-white p-6 rounded-lg shadow-md">
            <div class="text-lg font-semibold">{{ $requests->where('status', 'approved')->count() }}</div>
            <div class="text-sm">Disetujui</div>
        </div>
        <div class="bg-red-500 text-white p-6 rounded-lg shadow-md">
            <div class="text-lg font-semibold">{{ $requests->where('status', 'rejected')->count() }}</div>
            <div class="text-sm">Ditolak</div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6 min-w-0">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribusi Status Pengajuan</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="status-pie-chart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6 min-w-0">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribusi Cluster</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="cluster-bar-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-4 gap-4">
            <h3 class="text-lg font-semibold">Filter & Quick Actions</h3>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('apd-requests.admin-index') }}"
                   class="bg-yellow-500 text-white px-3 py-2 rounded-md text-sm hover:bg-yellow-600 flex items-center gap-2 {{ ($statusFilter == 'pending') ? 'ring-2 ring-yellow-300' : '' }}">
                    <i class="fas fa-clock"></i>
                    <span class="hidden sm:inline">Pending</span> ({{ $allRequests->where('status', 'pending')->count() }})
                </a>
                <a href="{{ route('apd-requests.admin-history') }}"
                   class="bg-green-500 text-white px-3 py-2 rounded-md text-sm hover:bg-green-600 flex items-center gap-2">
                    <i class="fas fa-history"></i>
                    <span class="hidden sm:inline">Riwayat</span>
                </a>
                <a href="{{ route('apd-requests.admin-index', ['status' => 'all']) }}"
                   class="bg-blue-500 text-white px-3 py-2 rounded-md text-sm hover:bg-blue-600 flex items-center gap-2 {{ ($statusFilter == 'all') ? 'ring-2 ring-blue-300' : '' }}">
                    <i class="fas fa-list"></i>
                    <span class="hidden sm:inline">Semua</span>
                </a>
            </div>
        </div>
        <form method="GET" action="{{ route('apd-requests.admin-index') }}" class="flex flex-col sm:flex-row flex-wrap items-start sm:items-center gap-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                <label for="status_filter" class="text-sm font-medium text-gray-700 whitespace-nowrap">Filter Status:</label>
                <select name="status" id="status_filter" class="px-3 py-2 border border-gray-300 rounded-md text-sm min-w-0" onchange="this.form.submit()">
                    <option value="">Pending (Default)</option>
                    <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $statusFilter == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ $statusFilter == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                <label for="cluster_filter" class="text-sm font-medium text-gray-700 whitespace-nowrap">Filter Cluster:</label>
                <select name="cluster" id="cluster_filter" class="px-3 py-2 border border-gray-300 rounded-md text-sm min-w-0" onchange="this.form.submit()">>
                    <option value="">Semua Cluster</option>
                    @foreach($requests->pluck('nama_cluster')->unique()->filter() as $cluster)
                        <option value="{{ $cluster }}" {{ request('cluster') == $cluster ? 'selected' : '' }}>{{ $cluster }}</option>
                    @endforeach
                </select>
            </div>
            @if($statusFilter !== 'pending' || request()->filled('cluster'))
                <a href="{{ route('apd-requests.admin-index') }}" class="text-sm bg-gray-500 text-white px-3 py-2 rounded-md hover:bg-gray-600">
                    Reset ke Pending
                </a>
            @endif
        </form>
    </div>

    @if($requests->isEmpty())
        <p class="text-gray-600">Belum ada pengajuan APD.</p>
    @else
    <div class="overflow-x-auto bg-white rounded-lg shadow-md">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nomor Pengajuan</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tim Mandor</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah APD</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cluster</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">PIC</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Disetujui</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($requests as $req)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $req->nomor_pengajuan }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $req->team_mandor }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $req->jumlah_apd }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $req->nama_cluster }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $req->user->name }}</td>
                        <td class="px-4 py-2 text-sm">
                            @if($req->status === 'approved')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Disetujui</span>
                            @elseif($req->status === 'rejected')
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Ditolak</span>
                            @else
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-500">{{ $req->approved_at ? $req->approved_at->format('d M Y') : '-' }}</td>
                        <td class="px-4 py-2 text-sm font-medium">
                            <a href="{{ route('apd-requests.show', $req) }}" class="text-blue-600 hover:text-blue-800 mr-2">Detail</a>
                            @if($req->status === 'pending')
                                <form action="{{ route('apd-requests.approve', $req) }}" method="POST" onsubmit="return confirm('Setujui pengajuan APD ini?');" class="inline-block mr-2">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800">Setujui</button>
                                </form>
                                <form action="{{ route('apd-requests.reject', $req) }}" method="POST" onsubmit="return confirm('Tolak pengajuan APD ini?');" class="inline-block">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-800">Tolak</button>
                                </form>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data untuk chart status
    const statusData = {
        labels: ['Pending', 'Disetujui', 'Ditolak'],
        datasets: [{
            data: [
                {{ $requests->where('status', 'pending')->count() }},
                {{ $requests->where('status', 'approved')->count() }},
                {{ $requests->where('status', 'rejected')->count() }}
            ],
            backgroundColor: ['#F59E0B', '#10B981', '#EF4444']
        }]
    };
    const statusCtx = document.getElementById('status-pie-chart');
    if (statusCtx) new Chart(statusCtx, { type: 'pie', data: statusData, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } } });

    // Data untuk chart cluster
    const clusterLabels = @json($requests->pluck('nama_cluster')->unique()->values());
    const clusterCounts = @json($requests->groupBy('nama_cluster')->map->count()->values());
    const clusterData = {
        labels: clusterLabels,
        datasets: [{
            label: 'Jumlah Pengajuan',
            data: clusterCounts,
            backgroundColor: '#3B82F6'
        }]
    };
    const clusterCtx = document.getElementById('cluster-bar-chart');
    if (clusterCtx) new Chart(clusterCtx, { type: 'bar', data: clusterData, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } } });
});
</script>
@endpush
