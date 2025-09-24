@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Pengajuan Reimburse</h1>
            <div class="text-sm text-gray-600">
                <i class="fas fa-info-circle mr-1"></i>
                Kelola pengajuan reimburse yang masuk
            </div>
        </div>

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

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-blue-500 text-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium">Total Reimburse</p>
                        <p class="text-2xl font-bold">{{ $requests->count() }}</p>
                    </div>
                    <div class="p-3 bg-white rounded-lg">
                        <i class="fas fa-receipt text-blue-500"></i>
                    </div>
                </div>
            </div>
            <div class="bg-yellow-500 text-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium">Pending</p>
                        <p class="text-2xl font-bold">{{ $requests->where('status', 'pending')->count() }}</p>
                    </div>
                    <div class="p-3 bg-white rounded-lg">
                        <i class="fas fa-clock text-yellow-500"></i>
                    </div>
                </div>
            </div>
            <div class="bg-green-500 text-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium">Disetujui</p>
                        <p class="text-2xl font-bold">{{ $requests->where('status', 'approved')->count() }}</p>
                    </div>
                    <div class="p-3 bg-white rounded-lg">
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                </div>
            </div>
            <div class="bg-purple-500 text-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium">Total Nilai</p>
                        <p class="text-lg font-bold">Rp {{ number_format($requests->where('status', 'approved')->sum('biaya'), 0, ',', '.') }}</p>
                    </div>
                    <div class="p-3 bg-white rounded-lg">
                        <i class="fas fa-money-bill text-purple-500"></i>
                    </div>
                </div>
            </div>
        </div>

    @if(request('status') != 'pending')
    <!-- Charts Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold mb-4">Status Reimburse</h3>
                <div style="height:300px; position:relative;">
                    <canvas id="reimburseStatusChart"></canvas>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold mb-4">Biaya Reimburse per Bulan</h3>
                <div style="height:300px; position:relative;">
                    <canvas id="reimburseMonthlyChart"></canvas>
                </div>
            </div>
    </div>
    @endif

        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Filter & Quick Actions</h3>
                <div class="flex gap-2">
                    <a href="{{ route('reimburse-requests.admin-index', ['status' => 'pending']) }}"
                       class="bg-yellow-500 text-white px-4 py-2 rounded-md text-sm hover:bg-yellow-600 flex items-center gap-2 {{ request('status') == 'pending' ? 'ring-2 ring-yellow-300' : '' }}">
                        <i class="fas fa-clock"></i>
                        Pending ({{ $requests->where('status', 'pending')->count() }})
                    </a>
                    <a href="{{ route('reimburse-requests.admin-index') }}"
                       class="bg-blue-500 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-600 flex items-center gap-2 {{ !request()->hasAny(['status', 'month', 'min_amount']) ? 'ring-2 ring-blue-300' : '' }}">
                        <i class="fas fa-list"></i>
                        Semua
                    </a>
                    <a href="{{ route('reimburse-requests.admin-index.export-csv', request()->query()) }}" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 flex items-center gap-2">
                        <i class="fas fa-download"></i>
                        Export CSV
                    </a>
                </div>
            </div>
            <form method="GET" action="{{ route('reimburse-requests.admin-index') }}" class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2">
                    <label for="status_filter" class="text-sm font-medium text-gray-700">Filter Status:</label>
                    <select name="status" id="status_filter" class="px-3 py-2 border border-gray-300 rounded-md text-sm" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label for="month_filter" class="text-sm font-medium text-gray-700">Filter Bulan:</label>
                    <select name="month" id="month_filter" class="px-3 py-2 border border-gray-300 rounded-md text-sm" onchange="this.form.submit()">
                        <option value="">Semua Bulan</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label for="min_amount" class="text-sm font-medium text-gray-700">Min Biaya:</label>
                    <input type="number" name="min_amount" id="min_amount" value="{{ request('min_amount') }}"
                           class="px-3 py-2 border border-gray-300 rounded-md text-sm w-32"
                           placeholder="0" onchange="this.form.submit()">
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
                @if(request()->hasAny(['status', 'month', 'min_amount', 'search']))
                    <a href="{{ route('reimburse-requests.admin-index') }}" class="text-sm bg-gray-500 text-white px-3 py-2 rounded-md hover:bg-gray-600">
                        Reset Filter
                    </a>
                @endif
            </form>
        </div>

        <form id="deleteForm" action="{{ route('reimburse-requests.admin-index.bulk-delete') }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="mb-4 flex gap-2">
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700" onclick="return confirm('Yakin ingin menghapus terpilih?');">
                    <i class="fas fa-trash mr-2"></i>Hapus Terpilih
                </button>
            </div>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            @if($requests->count() > 0)
                <!-- Mobile: stacked cards -->
                <div class="md:hidden p-4 space-y-3">
                    @foreach($requests as $request)
                        <div class="border border-gray-100 rounded-lg p-3 bg-white shadow-sm">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $request->nomor_pengajuan }}</div>
                                    <div class="text-xs text-gray-500">{{ $request->user->name }} &middot; {{ $request->user->email }}</div>
                                </div>
                                <div class="text-sm font-bold text-gray-800">Rp {{ number_format($request->biaya, 0, ',', '.') }}</div>
                            </div>
                            <div class="mt-2 text-sm text-gray-700">
                                <div>{{ $request->asset->merk ?? 'N/A' }} - {{ $request->asset->model ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($request->tanggal_service)->format('d/m/Y') }}</div>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <div>
                                    @if($request->status == 'pending')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    @elseif($request->status == 'approved')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Disetujui</span>
                                    @elseif($request->status == 'rejected')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('reimburse-requests.show', $request) }}" class="text-blue-600 hover:text-blue-900 p-2 rounded-md" aria-label="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($request->status == 'pending')
                                        <form method="POST" action="{{ route('reimburse-requests.approve', $request) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900 p-2 rounded-md" onclick="return confirm('Setujui reimburse ini?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('reimburse-requests.reject', $request) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-900 p-2 rounded-md" onclick="return confirm('Tolak reimburse ini?')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Desktop / Tablet: table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3">
                                    <input type="checkbox" id="selectAll" />
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nomor Pengajuan
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Asset
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Biaya
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Service
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($requests as $request)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="ids[]" value="{{ $request->id }}" class="rowCheckbox" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $request->nomor_pengajuan }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $request->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $request->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $request->asset->merk ?? 'N/A' }} - {{ $request->asset->model ?? 'N/A' }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $request->asset->nomor_polisi ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            Rp {{ number_format($request->biaya, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($request->tanggal_service)->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($request->status == 'pending')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                        @elseif($request->status == 'approved')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Disetujui
                                            </span>
                                        @elseif($request->status == 'rejected')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('reimburse-requests.show', $request) }}"
                                               class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if($request->status == 'pending')
                                                <form method="POST" action="{{ route('reimburse-requests.approve', $request) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900"
                                                            onclick="return confirm('Setujui reimburse ini?')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('reimburse-requests.reject', $request) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                                            onclick="return confirm('Tolak reimburse ini?')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6 text-center">
                    <i class="fas fa-receipt text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Belum ada reimburse request.</p>
                </div>
            @endif
        </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Reimburse Status Chart
    const statusCtx = document.getElementById('reimburseStatusChart').getContext('2d');
    const statusData = {
        labels: ['Pending', 'Disetujui', 'Ditolak'],
        datasets: [{
            data: [
                {{ $requests->where('status', 'pending')->count() }},
                {{ $requests->where('status', 'approved')->count() }},
                {{ $requests->where('status', 'rejected')->count() }}
            ],
            backgroundColor: [
                '#fbbf24',
                '#10b981',
                '#ef4444'
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    };

    new Chart(statusCtx, {
        type: 'doughnut',
        data: statusData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Monthly Reimburse Chart
    const monthlyCtx = document.getElementById('reimburseMonthlyChart').getContext('2d');

    // Precomputed monthly totals from controller
    const monthlyData = @json($monthlyData);

    const monthLabels = Object.keys(monthlyData).map(month => {
        const date = new Date(month + '-01');
        return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'short' });
    });
    const monthValues = Object.values(monthlyData);

    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Biaya Reimburse (Rp)',
                data: monthValues,
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Checkbox functionality
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = selectAll.checked);
        });
    }
});
</script>
@endsection
