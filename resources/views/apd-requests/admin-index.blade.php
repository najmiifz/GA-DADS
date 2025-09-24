@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="w-full px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                @if($statusFilter === 'pending')
                    Pengajuan APD
                    <span class="text-lg text-yellow-600">(Menunggu Persetujuan)</span>
                @elseif($statusFilter === 'all')
                    Semua Aktivitas Pengajuan APD
                @else
                    Aktivitas Pengajuan APD ({{ ucfirst($statusFilter) }})
                @endif
            </h1>
            @if($statusFilter === 'pending')
                <p class="text-sm text-gray-600 mt-2">Kelola pengajuan APD yang menunggu persetujuan oleh admin.</p>
            @elseif($statusFilter === 'all')
                <p class="text-sm text-gray-600 mt-2">Melihat semua aktivitas pengajuan APD (termasuk pending, disetujui, dan ditolak).</p>
            @endif
        </div>
        <!-- Reset Stocks Button -->
        <div class="flex justify-end mb-6 px-4">
            <form action="{{ route('apd-requests.reset-stocks') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm">
                    Reset Semua Stock ke 50
                </button>
            </form>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-blue-500 text-white p-4 rounded-lg shadow">
                <div class="text-xl font-semibold">{{ $allRequests->count() }}</div>
                <div class="text-sm opacity-90">Total Pengajuan</div>
            </div>
            <div class="bg-yellow-500 text-white p-4 rounded-lg shadow">
                <div class="text-xl font-semibold">{{ $allRequests->where('status', 'pending')->count() }}</div>
                <div class="text-sm opacity-90">Pending</div>
            </div>
            <div class="bg-blue-600 text-white p-4 rounded-lg shadow">
                <div class="text-xl font-semibold">{{ $allRequests->where('status', 'delivery')->count() }}</div>
                <div class="text-sm opacity-90">Dikirim</div>
            </div>
                <div class="bg-green-500 text-white p-4 rounded-lg shadow">
                    <div class="text-xl font-semibold">{{ $allRequests->where('status', 'received')->count() }}</div>
                    <div class="text-sm opacity-90">Diterima</div>
                </div>
            <div class="bg-red-500 text-white p-4 rounded-lg shadow">
                <div class="text-xl font-semibold">{{ $allRequests->where('status', 'rejected')->count() }}</div>
                <div class="text-sm opacity-90">Ditolak</div>
            </div>
        </div>

        <!-- Stock Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
            @foreach($stockItems as $name => $count)
                <div class="bg-gray-200 text-gray-800 p-4 rounded-lg shadow">
                    <div class="text-xl font-semibold">{{ $count }}</div>
                    <div class="text-sm opacity-90">{{ ucwords(str_replace('_', ' ', $name)) }} Stock</div>
                </div>
            @endforeach
        </div>

        @if($statusFilter === 'all')
        <!-- Charts Section (only for All Activities) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribusi Status Pengajuan</h3>
                <div class="h-64 relative">
                    <canvas id="status-pie-chart"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribusi Cluster</h3>
                <div class="h-64 relative">
                    <canvas id="cluster-bar-chart"></canvas>
                </div>
            </div>
        </div>
        @endif

    <!-- Quick Actions & Filter -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-4 gap-4">
                <h3 class="text-lg font-semibold">Filter & Quick Actions</h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('apd-requests.admin-index', ['status' => 'pending']) }}"
                       class="bg-yellow-500 text-white px-3 py-2 rounded text-sm hover:bg-yellow-600 flex items-center gap-2 {{ ($statusFilter == 'pending') ? 'ring-2 ring-yellow-300' : '' }}">
                        <i class="fas fa-clock"></i>
                        <span>Pending ({{ $allRequests->where('status', 'pending')->count() }})</span>
                    </a>
                    <a href="{{ route('apd-requests.admin-index', ['status' => 'delivery']) }}"
                       class="bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700 flex items-center gap-2 {{ ($statusFilter == 'delivery') ? 'ring-2 ring-blue-300' : '' }}">
                        <i class="fas fa-truck"></i>
                        <span>Dikirim ({{ $allRequests->where('status', 'delivery')->count() }})</span>
                    </a>
                    <a href="{{ route('apd-requests.admin-history') }}"
                       class="bg-green-500 text-white px-3 py-2 rounded text-sm hover:bg-green-600 flex items-center gap-2 {{ (request()->routeIs('apd-requests.admin-history')) ? 'ring-2 ring-green-300' : '' }}">
                        <i class="fas fa-history"></i>
                        <span>Riwayat</span>
                    </a>
                    <a href="{{ route('apd-requests.admin-index', ['status' => 'all']) }}"
                       class="bg-blue-500 text-white px-3 py-2 rounded text-sm hover:bg-blue-600 flex items-center gap-2 {{ ($statusFilter == 'all') ? 'ring-2 ring-blue-300' : '' }}">
                        <i class="fas fa-list"></i>
                        <span>Semua</span>
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('apd-requests.admin-index') }}" class="flex flex-col sm:flex-row gap-4">
                <div class="flex flex-col gap-2">
                    <label for="status_filter" class="text-sm font-medium text-gray-700">Filter Status:</label>
                    <select name="status" id="status_filter" class="px-3 py-2 border border-gray-300 rounded text-sm" onchange="this.form.submit()">
                        <option value="">Pending (Default)</option>
                        <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="delivery" {{ $statusFilter == 'delivery' ? 'selected' : '' }}>Dikirim</option>
                        <option value="approved" {{ $statusFilter == 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="received" {{ $statusFilter == 'received' ? 'selected' : '' }}>Diterima</option>
                        <option value="rejected" {{ $statusFilter == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="flex flex-col gap-2">
                    <label for="cluster_filter" class="text-sm font-medium text-gray-700">Filter Cluster:</label>
                    <select name="cluster" id="cluster_filter" class="px-3 py-2 border border-gray-300 rounded text-sm" onchange="this.form.submit()">
                        <option value="">Semua Cluster</option>
                        @foreach($requests->pluck('nama_cluster')->unique()->filter() as $cluster)
                            <option value="{{ $cluster }}" {{ request('cluster') == $cluster ? 'selected' : '' }}>{{ $cluster }}</option>
                        @endforeach
                    </select>
                </div>
                @if($statusFilter !== 'pending' || request()->filled('cluster'))
                    <div class="flex items-end">
                        <a href="{{ route('apd-requests.admin-index') }}" class="text-sm bg-gray-500 text-white px-3 py-2 rounded hover:bg-gray-600">
                            Reset ke Pending
                        </a>
                    </div>
                @endif
            </form>
        </div>
        @if($statusFilter === 'all')
            <!-- Export & Bulk Delete Actions -->
            <div class="flex justify-end mb-4 px-4 gap-2">
                <a href="{{ route('apd-requests.admin-index.export-csv', request()->query()) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm flex items-center gap-2">
                    <i class="fas fa-download"></i>
                    <span>Export CSV</span>
                </a>
                <!-- The delete button targets a hidden form (see script below) to avoid nesting forms which breaks inner submit buttons -->
                <button type="button" id="bulkDeleteBtn" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm flex items-center gap-2">
                    <i class="fas fa-trash"></i>
                    <span>Hapus Terpilih</span>
                </button>
            </div>
            <!-- Hidden form used to submit bulk-delete (keeps table rows and inline forms separate) -->
            <form id="deleteForm" action="{{ route('apd-requests.admin-index.bulk-delete') }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
                <div id="bulkDeleteInputs"></div>
            </form>
        @endif

        <!-- Data Table -->
    @if($requests->isEmpty())
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-600">Belum ada pengajuan APD.</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">
                        <table class="min-w-full divide-y divide-gray-200">
                         <thead class="bg-gray-50">
                             <tr>
                                    @if($statusFilter === 'all')
                                        <th class="px-2 py-2 sm:px-4 sm:py-3"><input type="checkbox" id="selectAll" class="rowCheckbox" /></th>
                                    @endif
                                     <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                     <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pengajuan</th>
                                     <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                     <th class="hidden sm:table-cell px-2 py-2 sm:px-4 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Helm</th>
                                     <th class="hidden sm:table-cell px-2 py-2 sm:px-4 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rompi</th>
                                     <th class="hidden sm:table-cell px-2 py-2 sm:px-4 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AP Boots</th>
                                     <th class="hidden sm:table-cell px-2 py-2 sm:px-4 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Body Harness</th>
                                     <th class="hidden sm:table-cell px-2 py-2 sm:px-4 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sarung Tangan</th>
                                     <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cluster</th>
                                     <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC</th>
                                     <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                     <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Disetujui</th>
                                     <th class="px-2 py-2 sm:px-4 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                             </tr>
                         </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($requests as $req)
                            <tr class="hover:bg-gray-50">
                                @if($statusFilter === 'all')
                                        <td class="px-2 py-2 sm:px-4 sm:py-3 whitespace-nowrap"><input type="checkbox" name="ids[]" value="{{ $req->id }}" class="rowCheckbox" /></td>
                                @endif
                                     <td class="px-2 py-2 sm:px-4 sm:py-3 text-sm text-gray-900">{{ $loop->iteration }}</td>
                                     <td class="px-2 py-2 sm:px-4 sm:py-3 text-sm text-gray-900">{{ $req->nomor_pengajuan }}</td>
                                     <td class="px-2 py-2 sm:px-4 sm:py-3 text-sm text-gray-900">{{ $req->user->name }}</td>
                                     <td class="hidden sm:table-cell px-2 py-2 sm:px-4 sm:py-3 text-sm text-gray-900">{{ $req->helm }}</td>
                                     <td class="hidden sm:table-cell px-2 py-2 sm:px-4 sm:py-3 text-sm text-gray-900">{{ $req->rompi }}</td>
                                     <td class="hidden sm:table-cell px-2 py-2 sm:px-4 sm:py-3 text-sm text-gray-900">{{ $req->apboots }}</td>
                                     <td class="hidden sm:table-cell px-2 py-2 sm:px-4 sm:py-3 text-sm text-gray-900">{{ $req->body_harness }}</td>
                                     <td class="hidden sm:table-cell px-2 py-2 sm:px-4 sm:py-3 text-sm text-gray-900">{{ $req->sarung_tangan }}</td>
                                     <td class="px-2 py-2 sm:px-4 sm:py-3 text-sm text-gray-900">{{ $req->nama_cluster }}</td>
                                     <td class="px-2 py-2 sm:px-4 sm:py-3 text-sm text-gray-900">{{ $req->user->name }}</td>
                                <td class="px-2 py-2 sm:px-4 sm:py-3 text-sm">
                                    @php $state = strtolower($req->status ?? 'pending'); @endphp
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
                                <td class="px-2 py-2 sm:px-4 sm:py-3 text-sm text-gray-500">{{ $req->approved_at ? $req->approved_at->format('d M Y') : '-' }}</td>
                                <td class="px-2 py-2 sm:px-4 sm:py-3 text-sm space-x-2">
                                    @php $state = strtolower($req->status ?? 'pending'); @endphp
                                    @if($state === 'pending')
                                        <div class="flex flex-wrap gap-2">
                                            <form action="{{ route('apd-requests.approve', ['apdRequest' => $req->id]) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 bg-green-600 text-white rounded-md text-xs hover:bg-green-700 transition">Kirim</button>
                                            </form>
                                            <form action="{{ route('apd-requests.reject', ['apdRequest' => $req->id]) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded-md text-xs hover:bg-red-700 transition">Tolak</button>
                                            </form>
                                        </div>
                                    @elseif($state === 'delivery')
                                            @if(!$req->restocked)
                                            <form action="{{ route('apd-requests.restock', ['apdRequest' => $req->id]) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 text-purple-600 hover:text-purple-800 text-xs">Restock</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('apd-requests.show', ['apdRequest' => $req->id]) }}" class="px-2 py-1 text-blue-600 hover:text-blue-800 text-xs">Detail</a>
                                    @else
                                        <a href="{{ route('apd-requests.show', ['apdRequest' => $req->id]) }}" class="px-2 py-1 text-blue-600 hover:text-blue-800 text-xs">Detail</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @if($statusFilter === 'all')
        @endif
        @endif

    </div>
</div>

@if($statusFilter === 'all')
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status Chart
    const statusData = {
    labels: ['Pending', 'Diterima', 'Ditolak'],
        datasets: [{
            data: [
                {{ $requests->where('status', 'pending')->count() }},
                {{ $requests->where('status', 'received')->count() }},
                {{ $requests->where('status', 'rejected')->count() }}
            ],
            backgroundColor: ['#F59E0B', '#10B981', '#EF4444']
        }]
    };
    const statusCtx = document.getElementById('status-pie-chart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'pie',
            data: statusData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Cluster Chart
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
    if (clusterCtx) {
        new Chart(clusterCtx, {
            type: 'bar',
            data: clusterData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
});
</script>
@endpush
@endif

@endsection

@if($statusFilter === 'all')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    if (!bulkDeleteBtn) return;

    bulkDeleteBtn.addEventListener('click', function() {
        const checkboxes = Array.from(document.querySelectorAll('input[name="ids[]"]:checked'));
        if (checkboxes.length === 0) {
            alert('Pilih minimal satu pengajuan untuk dihapus.');
            return;
        }
        if (!confirm(`Yakin ingin menghapus ${checkboxes.length} pengajuan terpilih?`)) return;

        const deleteForm = document.getElementById('deleteForm');
        const inputsContainer = document.getElementById('bulkDeleteInputs');
        inputsContainer.innerHTML = '';
        checkboxes.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            inputsContainer.appendChild(input);
        });

        deleteForm.submit();
    });
});
</script>
@endpush
@endif
